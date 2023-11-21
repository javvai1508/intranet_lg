(function ($, undefined) {
    $.nette.ext({
        load: function () {
            $('[data-nette-confirm]').off('click.nette').click(function (event) {
                
                if ($(this).attr("disabled") == "disabled") {
                    return false;
                } else {
                    // remove old confirm dialog
                    $("#dConfirmMaster").remove();

                    var obj = this;
                    event.preventDefault();
                    event.stopImmediatePropagation();

                    // modal dialog skeleton
                    $("<div class='modal fade' id='dConfirmMaster' tabindex='-1' role='dialog' aria-labelledby='confirmModalLabel' aria-hidden='true'>" +
                            "<div class='modal-dialog'>" +
                            "	<div class='modal-content' id='dConfirm'>" +
                            "		<div class='modal-header'>" +
                            "			<h4 class='modal-title' warning id='confirmModalLabel'>" + $(obj).data('nette-confirm-title') + "</h4>" +
                            "			<button type='button' class='close' data-dismiss='modal' aria-hidden='true'>&times;</button>" +
                            "		</div>" +
                            "		<div class='modal-body'><strong>" +
                            $(obj).data('nette-confirm-text') +
                            "		</strong></div>" +
                            "		<div class='modal-footer'>" +
                            "			<button type='button' id='dConfirmCancel' class='btn btn-lg m5 " + $(obj).data('nette-confirm-cancel-class') + "' data-dismiss='modal'>Ne</button>" +
                            "			<button type='button' id='dConfirmOk' class='btn btn-lg m5 " + $(obj).data('nette-confirm-ok-class') + "' data-dismiss='modal'>Ano</button>" +
                            "		</div>" +
                            "	</div>" +
                            "</div>" +
                            "</div>").appendTo('body');

                    if ($(obj).data('nette-confirm-ok-text')) {
                        $('#dConfirmOk').html($(obj).data('nette-confirm-ok-text'));
                    }

                    if ($(obj).data('nette-confirm-cancel-text')) {
                        $('#dConfirmCancel').html($(obj).data('nette-confirm-cancel-text'));
                    }
                    
                    $('#dConfirmOk').click(function () {
                        var tagName = $(obj).prop("tagName");
                        if (tagName == 'INPUT') {
                            var form = $(obj).closest('form');
                            form.submit();
                        } else {
                            if ($(obj).hasClass('ajax') || $(obj).data('ajax') == 'on') {
                                $.nette.ajax({
                                    url: obj.href
                                });
                            } else {
                                document.location = obj.href;
                            }
                        }
                    });

                    $('#dConfirmMaster').modal('show');

                    return false;
                }
            });
        }
    });
})(jQuery);
