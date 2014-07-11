function RcmNavMenuHelper(dialog) {
    var me = this;

    me.dialog = dialog;

    me.init = function () {
        jQuery(".RcmAdminMenu").find("a").click(function (event) {
            event.preventDefault();

            var link = jQuery(this);

            var url = link.attr('href');

            var title = link.attr('title');

            if (link.parent().hasClass('RcmForm')) {
                me.dialog.switchStrategy('RcmForm')
            }

            me.dialog.load(url, title);
        });
    };

    me.setDialog = function (dialog) {
        me.dialog = dialog;
    };

    me.getDialog = function getDialog() {
        return me.dialog;
    };
}