/*
* Read aloud ("Seite vorlesen" floater, all pages): reads the main content sentence by sentence with a German Web Speech API voice and highlights the current block
*/

(function () {
    "use strict";

    const root = document.querySelector("[data-read-aloud]");

    if (!root) return;

    const toggle = root.querySelector("[data-read-aloud-toggle]");
    const stopButton = root.querySelector("[data-read-aloud-stop]");
    const label = root.querySelector("[data-read-aloud-label]");
    const reduceMotion = window.matchMedia("(prefers-reduced-motion: reduce)");

    const SKIP = [
        "script", "style", "noscript", "template", "svg", "canvas", "iframe", "video", "audio", "img", "picture",
        "input", "select", "textarea", "[hidden]", "[inert]", "[aria-hidden='true']",
        "details:not([open]) > :not(summary)", "[data-read-aloud]", "[data-read-aloud-skip]",
    ].join(", ");
    const VISUALLY_HIDDEN = ".screen-reader-text, .visually-hidden, .sr-only";
    const CURRENT = "is-read-aloud";
    const MAX_CHUNK = 160;
    const BREAK = " ";
    const ABBREVIATION = /(?:\b\d{1,2}|\b(?:z\. ?B|u\. ?a|ca|Nr|inkl|bzw|evtl|ggf|vgl|resp|Min|Std|Str|Tel|Fr|Hr|Dr|St))\.\s*$/i;

    const engine = {
        voice: null,
        utterance: null,

        supported: function () {
            return "speechSynthesis" in window && typeof window.SpeechSynthesisUtterance === "function";
        },

        pickVoice: function () {
            const rank = function (voice) {
                const lang = String(voice.lang || "").replace("_", "-").toLowerCase();

                if (lang === "de-ch") return 0;
                if (lang === "de-de") return 1;
                if (lang === "de" || lang.indexOf("de-") === 0) return 2;

                return -1;
            };

            let best = null;
            let bestRank = 9;

            window.speechSynthesis.getVoices().forEach(function (voice) {
                const r = rank(voice);

                if (r < 0) return;

                if (r < bestRank || (r === bestRank && voice.default && !best.default)) {
                    best = voice;
                    bestRank = r;
                }
            });

            if (best) this.voice = best;

            return best;
        },

        whenReady: function (callback) {
            const synth = window.speechSynthesis;
            const self = this;
            let tries = 0;
            let timer = null;

            const finish = function (ok) {
                window.clearInterval(timer);

                if (synth.removeEventListener) synth.removeEventListener("voiceschanged", check);

                callback(ok);
            };

            const check = function () {
                if (self.pickVoice()) finish(true);
                else if (++tries > 20) finish(false);
            };

            if (this.pickVoice()) {
                callback(true);
                return;
            }

            if (synth.addEventListener) synth.addEventListener("voiceschanged", check);

            timer = window.setInterval(check, 250);
        },

        speak: function (text, onEnd, onError) {
            const synth = window.speechSynthesis;
            const utterance = new window.SpeechSynthesisUtterance(text);

            utterance.voice = this.voice;
            utterance.lang = this.voice.lang;
            utterance.onend = onEnd;
            utterance.onerror = onError;

            this.utterance = utterance;

            if (synth.paused) synth.resume();

            synth.speak(utterance);
        },

        cancel: function () {
            this.utterance = null;
            window.speechSynthesis.cancel();
        },
    };

    const clean = function (text) {
        return text
            .replace(/[­​-‍﻿]/g, "")
            .replace(new RegExp("([.!?,;:])?\\s*" + BREAK + "\\s*", "g"), function (match, mark) {
                return (mark || ",") + " ";
            })
            .replace(/(\d)['’](?=\d{3}\b)/g, "$1")
            .replace(/\s+[·•|]\s+/g, ", ")
            .replace(/\s+/g, " ")
            .replace(/^[\s,]+|[\s,]+$/g, "");
    };

    const sentences = function (text) {
        const parts = window.Intl && Intl.Segmenter
            ? Array.from(new Intl.Segmenter("de", { granularity: "sentence" }).segment(text), function (part) {
                return part.segment;
            })
            : text.match(/[^.!?…]+(?:[.!?…]+\S*\s*|$)/g) || [text];

        return parts.reduce(function (out, part) {
            if (out.length && ABBREVIATION.test(out[out.length - 1])) out[out.length - 1] += part;
            else out.push(part);

            return out;
        }, []);
    };

    const pieces = function (sentence) {
        if (sentence.length <= MAX_CHUNK) return [sentence];

        const out = [];
        let current = "";

        sentence.split(" ").forEach(function (word) {
            if (current && current.length + word.length + 1 > MAX_CHUNK) {
                out.push(current);
                current = "";
            }

            current = current ? current + " " + word : word;

            if (current.length > MAX_CHUNK / 2 && /[,;:–—]$/.test(word)) {
                out.push(current);
                current = "";
            }
        });

        if (current) out.push(current);

        return out;
    };

    const collect = function () {
        const main = document.querySelector("main") || document.body;
        const inline = new Map();
        const blocks = [];
        let last = null;

        const isInline = function (el) {
            if (!inline.has(el)) {
                const display = window.getComputedStyle(el).display;

                inline.set(el, display === "inline" || display === "contents" || el.matches(VISUALLY_HIDDEN));
            }

            return inline.get(el);
        };

        const blockOf = function (node) {
            let el = node.parentElement;

            while (el && el !== main && isInline(el)) el = el.parentElement;

            return el || main;
        };

        const walker = document.createTreeWalker(main, NodeFilter.SHOW_ELEMENT | NodeFilter.SHOW_TEXT, {
            acceptNode: function (node) {
                if (node.nodeType === 3) {
                    return node.nodeValue ? NodeFilter.FILTER_ACCEPT : NodeFilter.FILTER_SKIP;
                }

                if (node.matches(SKIP) || window.getComputedStyle(node).display === "none") return NodeFilter.FILTER_REJECT;

                return node.tagName === "BR" ? NodeFilter.FILTER_ACCEPT : NodeFilter.FILTER_SKIP;
            },
        });

        let node;

        while ((node = walker.nextNode())) {
            const block = blockOf(node);
            const text = node.nodeType === 3 ? node.nodeValue : BREAK;

            if (last && last.el === block) {
                last.text += text;
            } else {
                last = { el: block, text: text };
                blocks.push(last);
            }
        }

        const queue = [];

        blocks.forEach(function (block) {
            const text = clean(block.text);

            if (!/[\p{L}\p{N}]/u.test(text)) return;

            sentences(text).forEach(function (sentence) {
                pieces(sentence.trim()).forEach(function (piece) {
                    if (/[\p{L}\p{N}]/u.test(piece)) queue.push({ el: block.el, text: piece });
                });
            });
        });

        return queue;
    };

    let state = "idle";
    let queue = [];
    let index = 0;
    let run = 0;
    let current = null;
    let follow = true;

    const setState = function (next) {
        state = next;
        root.dataset.state = next;

        label.textContent = next === "playing"
            ? toggle.dataset.labelPause
            : next === "paused" ? toggle.dataset.labelResume : toggle.dataset.labelStart;

        if (next === "idle" && document.activeElement === stopButton) toggle.focus();

        stopButton.hidden = next === "idle";

        document.dispatchEvent(new CustomEvent("lr:read-aloud", { detail: { state: next } }));
    };

    const reveal = function (el) {
        const rect = el.getBoundingClientRect();
        const header = document.querySelector("[data-header]");
        const top = header ? Math.max(0, header.getBoundingClientRect().bottom) : 0;
        const bottom = root.getBoundingClientRect().top;

        if (rect.top >= top && rect.top < bottom - 32) return;

        el.scrollIntoView({
            block: rect.height < bottom - top ? "center" : "start",
            behavior: reduceMotion.matches ? "auto" : "smooth",
        });
    };

    const highlight = function (el) {
        if (current === el) return;

        if (current) current.classList.remove(CURRENT);

        current = el;

        if (!el) return;

        el.classList.add(CURRENT);

        if (follow) reveal(el);
    };

    const stop = function () {
        run += 1;
        engine.cancel();
        highlight(null);
        queue = [];
        index = 0;
        setState("idle");
    };

    const speakNext = function () {
        if (index >= queue.length) {
            stop();
            return;
        }

        const id = ++run;
        const item = queue[index];

        highlight(item.el);

        engine.speak(item.text, function () {
            if (id !== run) return;

            index += 1;
            speakNext();
        }, function (event) {
            if (id !== run || event.error === "interrupted" || event.error === "canceled") return;

            stop();
        });
    };

    const start = function () {
        queue = collect();
        index = 0;

        if (!queue.length) return;

        follow = true;
        setState("playing");
        speakNext();
    };

    const pause = function () {
        run += 1;
        engine.cancel();
        setState("paused");
    };

    const resume = function () {
        follow = true;
        setState("playing");
        reveal(queue[index].el);
        speakNext();
    };

    if (!engine.supported()) return;

    engine.cancel();

    engine.whenReady(function (ok) {
        if (!ok) return;

        root.hidden = false;

        toggle.addEventListener("click", function () {
            if (state === "playing") pause();
            else if (state === "paused") resume();
            else start();
        });

        stopButton.addEventListener("click", function () {
            stop();
            toggle.focus();
        });

        root.addEventListener("keydown", function (event) {
            if (event.key !== "Escape" || state === "idle") return;

            event.preventDefault();
            stop();
            toggle.focus();
        });

        ["wheel", "touchmove"].forEach(function (type) {
            window.addEventListener(type, function () {
                follow = false;
            }, { passive: true });
        });

        window.addEventListener("pagehide", stop);

        window.addEventListener("pageshow", function (event) {
            if (event.persisted) stop();
        });
    });
})();
