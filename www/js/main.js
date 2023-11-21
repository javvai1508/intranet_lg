/* 
 * 
 */
$(function () {
    $.nette.init();

    drawFlashMessage();

    drawNetteFormAlert();

    $.nette.ext('snippets').after(function ($el) {
        if ($el.is('#snippet--flash')) {
            drawFlashMessage();
        }
    });

    //** Start datepicker **/
    /*
     $.fn.datepicker.defaults.language = 'cs';
     $.fn.datepicker.defaults.autoclose = true;
     $.fn.datepicker.defaults.format = 'dd.mm.yyyy';
     
     $(".date").datepicker({});
     $(".input-daterange").datepicker({});
     */
    //** End datepicker **/

    $('.today').on('click', function () {
        var date = new Date(),
                yr = date.getFullYear(),
                monthNum = date.getMonth() + 1,
                month = monthNum < 10 ? '0' + monthNum : monthNum,
                day = date.getDate() < 10 ? '0' + date.getDate() : date.getDate(),
                newDate = day + '.' + month + '.' + yr;
        $("#frm-searchForm-od").val(newDate);
        $("#frm-searchForm-do").val(newDate);

    });

    $('.nullsearch').on('click', function () {
        $("#frm-searchForm-searchString").val('');

    });

    /* clickable table row */
    $(".clickable-row").click(function() {
        window.location = $(this).data("href");
    });

    /* edit quantity */
    $('body').on('click', '.quantity-span', function () {
        // alert("click");
        var itemSNId = $(this).attr('data-sn-id');
        $(this).hide();
        $('#editq-id-' + itemSNId).show();
        $('#frm-editQuantityForm-' + itemSNId).find('.quantity-form-input').val($(this).attr('data-sn-value'));
        $('#frm-editQuantityForm-' + itemSNId).find('.quantity-form-input').focus();
        $('#frm-editQuantityForm-' + itemSNId).find('.quantity-form-input').select();
    });

    $('body').on('blur', '.quantity-form-input', function () {
        // alert("blur");
        var itemSNId = $(this).parents('.edit-quantity-form').siblings('.quantity-span').attr('data-sn-id');
        $('#frm-editQuantityForm-' + itemSNId).submit();

        $(this).parents('.edit-quantity-form').hide();
        $(this).parents('.edit-quantity-form').siblings('.quantity-span').show();
    });

    /* ------------------ materiál zákazníka ---------------------------------*/
    /* změna čísla odběratele - dotáhnout název firmy */
    $("#frm-newCustMaterialForm-CisloOrg").on("input", function (e) {
        var el = $(this);
        // alert(el.closest("div").attr("odkaz") + "&cisloOrg=" + el.val());
        if (!(el.val() === "")) {
            $.nette.ajax({
                url: el.closest("div").attr("odkaz") + "&cisloOrg=" + el.val(),
                success: function (data) {
                    $("#frm-newCustMaterialForm-Firma").val(data);
                }

            });
        } else {
            $("#frm-newCustMaterialForm-Firma").val("");
        }
    });

    /* autocomplete firma */
    $("#frm-newCustMaterialForm-Firma").autocomplete({
        source: function (request, response) {
            $.nette.ajax({
                url: $("#frm-newCustMaterialForm-Firma").parent(".autocomplete").attr("odkaz") + '&term=' + request.term,
                success: function (data) {
                    var dataOut = new Array(data.length);
                    $.each(data, function (index, element) {
                        dataOut[index] = data[index].Firma;
                    });
                    response(dataOut);
                }

            });
        },
        delay: 500
    });

    /* po opuštění pole firma dotáhnout číslo organizace */
    $("#frm-newCustMaterialForm-Firma").blur(function () {
        var cisloOrg = $(this).val().substring($(this).val().indexOf("(") + 1, $(this).val().indexOf(")"));
        // alert(cisloOrg);
        $("#frm-newCustMaterialForm-CisloOrg").val(parseInt(cisloOrg));
    });

    /* ------------------ příjem materiálu ---------------------------------*/
    /* změna čísla odběratele - dotáhnout název firmy */
    $("#frm-setOrderForm-CisloOrg").on("input", function (e) {
        var el = $(this);
        // alert(el.closest("div").attr("odkaz") + "&cisloOrg=" + el.val());
        if (!(el.val() === "")) {
            $.nette.ajax({
                url: el.closest("div").attr("odkaz") + "&cisloOrg=" + el.val(),
                success: function (data) {
                    $("#frm-setOrderForm-Firma").val(data);
                }

            });
        } else {
            $("#frm-setOrderForm-Firma").val("");
        }
    });

    /* autocomplete firma */
    $("#frm-setOrderForm-Firma").autocomplete({
        source: function (request, response) {
            $.nette.ajax({
                url: $("#frm-setOrderForm-Firma").parent(".autocomplete").attr("odkaz") + '&term=' + request.term,
                success: function (data) {
                    var dataOut = new Array(data.length);
                    $.each(data, function (index, element) {
                        dataOut[index] = data[index].Firma;
                    });
                    response(dataOut);
                }

            });
        },
        delay: 500
    });

    /* autocomplete číslo objednávky */
    $("#frm-setOrderForm-ParovaciZnak").autocomplete({
        source: function (request, response) {
            $.nette.ajax({
                url: $("#frm-setOrderForm-ParovaciZnak").parent(".autocomplete").attr("odkaz") + '&cisloOrg=' + $("#frm-setOrderForm-CisloOrg").val() + '&term=' + request.term,
                success: function (data) {
                    var dataOut = new Array(data.length);
                    $.each(data, function (index, element) {
                        dataOut[index] = data[index].ParovaciZnak;
                    });
                    response(dataOut);
                }

            });
        },
        delay: 500
    });

    /* po opuštění pole firma dotáhnout číslo organizace */
    $("#frm-setOrderForm-Firma").blur(function () {
        var cisloOrg = $(this).val().substring($(this).val().indexOf("(") + 1, $(this).val().indexOf(")"));
        // alert(cisloOrg);
        $("#frm-setOrderForm-CisloOrg").val(parseInt(cisloOrg));
    });

    /* po opuštění pole číslo objednávky dotáhnout údaje o firmě +  kontrola existence a stavu objednávky*/
    $("#frm-setOrderForm-ParovaciZnak").blur(function () {
        var el = $(this);
        // alert(el.closest("div").attr("odkaz") + "&cisloOrg=" + el.val());
        if (!(el.val() === "")) {
            $.nette.ajax({
                url: el.closest("div").attr("odkaz-blur") + "&parovaciZnak=" + el.val(),
                success: function (data) {
                    // $("#frm-setOrderForm-Firma").val(data);
                }

            });
        } else {
            $("#frm-setOrderForm-Firma").val("");
        }
    });

    /* generování výrobního čísla */
    $("#generateBCBtn").on("click", function (e) {
        e.preventDefault();
        var el = $(this);
        // alert(el.attr("odkaz"));
        $.nette.ajax({
            url: el.attr("odkaz"),
            success: function (data) {
                // alert(data);
                $("#frm-receiptReadBarCodeForm-BarCode").val(data);
            }

        });
    });

    /* na enter doplnit fokus na další input */
    $(".no-enter").on("keydown", function (e) {
        if (e.keyCode == 13) {
            e.preventDefault();
        }
    });

    /* inventura - výběr typu laminátu */
    $('#frm-startInventoryForm-TypLaminatu').change(function(){
        var attr = $('#selectTypLaminatu').attr('odkaz');
        attr = attr + '&typLaminatu=' + $('#frm-startInventoryForm-TypLaminatu').val();
        // alert('attr: ' + attr);
                
        $.nette.ajax({
            url: attr,
            type: 'POST',
            success: function (data) {
                // alert(JSON.stringify(data));
            }
        });
    });
    
    /* inventura - výběr průměru laminátu */
    $('#frm-startInventoryForm-Prumer1').change(function(){
        var attr = $('#selectPrumerLaminatu').attr('odkaz');
        attr = attr + '&typLaminatu=' + $('#frm-startInventoryForm-TypLaminatu').val() + '&prumerLaminatu=' + $('#frm-startInventoryForm-Prumer1').val();
        // alert('attr: ' + attr);
                
        $.nette.ajax({
            url: attr,
            type: 'POST',
            success: function (data) {
                // alert(JSON.stringify(data));
            }
        });
    });
    
    widthFunctions();

    $("input:text:visible:first").focus();
    // $("input[type=number]").is('visible').first().focus();
    // alert($("input[type=number]").length);
    $("select:visible:first").focus();
});

/* --------- listener pro redraw inventura ---------- */
function attachListener(){
    $('#frm-startInventoryForm-Prumer1').change(function(){
        var attr = $('#selectPrumerLaminatu').attr('odkaz');
        attr = attr + '&typLaminatu=' + $('#frm-startInventoryForm-TypLaminatu').val() + '&prumerLaminatu=' + $('#frm-startInventoryForm-Prumer1').val();
        // alert('attr: ' + attr);
                
        $.nette.ajax({
            url: attr,
            type: 'POST',
            success: function (data) {
                // alert(JSON.stringify(data));
            }
        });
    });
}

/* --------- Flash messages in modal window --------- */
function drawFlashMessage() {
    // alert('Hop sem' + $('.flash').length);
    if (!($('.flash').attr('data-message') === undefined)) {
        // alert('Hop tam');
        $("#dConfirmMaster").remove();

        var data = $('.flash').attr('data-message');
        var type = $('.flash').attr('data-message-type');
        var hd = '';

        if (type === 'error') {
            hd = 'Chyba';
        } else if (type === 'warning') {
            hd = 'Varování';
        } else {
            hd = 'Zpráva';
        }
        ;

        $("<div class='modal fade' id='dConfirmMaster' tabindex='-1' role='dialog' aria-labelledby='confirmModalLabel' aria-hidden='true'>" +
                "<div class='modal-dialog'>" +
                "	<div class='modal-content' id='dConfirm'>" +
                "		<div class='modal-header'>" +
                "			<h4 class='modal-title " + type + "' id='confirmModalLabel'>" + hd + "</h4>" +
                "			<button type='button' class='close' class='btn btn-lg' data-dismiss='modal' aria-hidden='true'>&times;</button>" +
                "		</div>" +
                "		<div class='modal-body'><strong>" +
                data +
                "		</strong></div>" +
                "		<div class='modal-footer'>" +
                "			<button type='button' id='dConfirmOk' class='btn btn-lg' data-dismiss='modal'>Zavřít</button>" +
                "		</div>" +
                "	</div>" +
                "</div>" +
                "</div>").appendTo('body');

        $('#dConfirmMaster').modal('show');
    }
}

/* nette forms alerts display in modal window */
function drawNetteFormAlert() {
    Nette.addError = function (elem, message) {

        if (message) {

// remove old confirm dialog
            $("#agErrorInfo").remove();
            event.preventDefault();
            event.stopImmediatePropagation();
            // modal dialog skeleton
            $("<div class='modal fade' id='agErrorInfo' tabindex='-1' role='dialog' aria-labelledby='confirmModalLabel' aria-hidden='true'>" +
                    "<div class='modal-dialog'>" +
                    "	<div class='modal-content' id='dConfirmMaster'>" +
                    "		<div class='modal-header'>" +
                    "			<h4 class='modal-title info' id='confirmModalLabel'>Chyba ve formuláři</h4>" +
                    "			<button type='button' class='close' class='btn btn-lg' data-dismiss='modal' aria-hidden='true'>&times;</button>" +
                    "		</div>" +
                    "		<div class='modal-body'><strong>" +
                    message +
                    "		</strong></div>" +
                    "		<div class='modal-footer'>" +
                    "			<button type='button' id='dConfirmOk' class='btn btn-lg' data-dismiss='modal'>Zavřít</button>" +
                    "		</div>" +
                    "	</div>" +
                    "</div>" +
                    "</div>").appendTo('body');
            $('#agErrorInfo').modal();
            $('#agErrorInfo').modal('show');
            $('#agErrorInfo').on('hidden.bs.modal', function () {
                if (elem.focus) {
                    elem.focus();
                }

            });
            //alert(message);
        }

    };

}

/* ---------- Page width functions ---------- */
$(window).on("resize", widthFunctions);
// $(document).on("ready", widthFunctions);

function widthFunctions(e) {
    var contentHeight = $('.content').height();
    var contentHeightOuter = $('.content').outerHeight();

    var headerHeight = $('.header').outerHeight();
    var footerHeight = $('.footer').outerHeight() + 20;

    var winHeight = $(window).height();
    var winWidth = $(window).width();
    var contentPadding = parseInt($('.content').css('padding-top'));

    $('.content').css('min-height', winHeight - footerHeight);
    // alert('win:' + winHeight + '/hd:' + headerHeight + '/ft:' + footerHeight + '/ctPad:' + contentPadding + '???');
}
/* ---------- End of Page width functions ---------- */

