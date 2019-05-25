define(
    [
        'jquery',
        'core/modal_factory',

    ],
    /**
     * A general purpose info dialog - makes it easy to show and hide a reusable dialog.
     * @param $
     * @param ModalFactory
     * @returns {dialogInfo}
     */
    function($, ModalFactory) {
        var dialogNumber = 0;

        return function DialogInfo(title, bodyHTML, footerHTML, large, autoShow) {
            var self = this;

            this.modal = null;
            this.dialogNum = 0;
            this.dialogIds = []; // Initialised dialog ids.

            this.restoreFooterDefault = function() {
                // TODO, localise OK.
                var id = 'info_dialog_' + this.dialogNum;
                var okId = id + '_ok';
                this.modal.setFooter('<button id="' + okId + '" class="btn btn-primary">OK</button>');

                if (this.dialogIds.indexOf(this.dialogNum) === -1) {
                    $('body').on('click', '#' + okId, function() {
                        self.modal.hide();
                    });
                }
            };

            this.show = function(title, bodyHTML, footerHTML, large) {
                this.modal.setTitle(title);
                this.modal.setBody(bodyHTML);
                if (footerHTML) {
                    this.modal.setFooter(footerHTML);
                } else {
                    this.restoreFooterDefault();
                }
                this.modal.setLarge(large ? true : false);
                this.modal.show();
            };

            this.hide = function() {
                this.modal.hide();
            };

            autoShow = autoShow === false ? false : true; // Default value is to auto show dialog on creation.

            if (this.modal) {
                var modal = this.modal;
                modal.setBody(bodyHTML);
                if (autoShow) {
                    modal.show();
                }
            } else {
                dialogNumber++;
                this.dialogNum = dialogNumber;

                ModalFactory.create({
                    title: title,
                    body: bodyHTML,
                    footer: footerHTML,
                    large: large,
                }).then(function(modal) {
                    self.modal = modal;
                    if (!footerHTML) {
                        self.restoreFooterDefault();
                    }
                    if (autoShow) {
                        modal.show();
                    }
                    self.dialogIds.push(self.dialogNum); // Dialog is now initialised so register id.
                });
            }
        };
    }
);