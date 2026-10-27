/*
* ACF admin: icon preview in "icon" select fields (Select2)
*/

(function ($) {
    "use strict";

    if (typeof acf === "undefined" || typeof lrAdmin === "undefined") return;

    const renderIcon = function (state) {
        if (!state.id) return state.text;

        return $('<span class="lr-icon-option"></span>')
            .append($("<img>", { src: lrAdmin.iconUrl + state.id + ".svg", width: 20, height: 20, alt: "" }).css({ verticalAlign: "middle", marginRight: "8px" }))
            .append(document.createTextNode(state.text));
    };

    acf.addFilter("select2_args", function (args, $select, settings, field) {
        if (!field || field.get("name") !== "icon") return args;

        args.templateResult = renderIcon;
        args.templateSelection = renderIcon;

        return args;
    });
})(jQuery);
