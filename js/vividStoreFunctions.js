var StoreDashboard = {};

StoreDashboard.Product = {
    init: function(){
        this.highlightProductGroupTab();
        this.setupProductMenu();
    },
    highlightProductGroupTab: function(){
        var url = window.location.toString();
        $('#group-filters li a').each(function(){
            var href = $(this).attr('href');
            if(url == href) {
                $(this).parent().addClass("active");
            }
        });
    },
    setupProductMenu: function(){
        $("a[data-pane-toggle]").click(function(e){
            e.preventDefault();
            var paneTarget = $(this).attr('href');
            paneTarget = paneTarget.replace('#','');
            $(".store-pane, a[data-pane-toggle]").removeClass('active');
            $('#'+paneTarget).addClass("active");
            $(this).addClass("active");
        });
    }
};

StoreDashboard.ProductGroup = {
    init: function(){
        this.setupButtons();
    },
    setupButtons: function(){
        $(".btn-delete-group").click(function(){
            StoreDashboard.ProductGroup.delete($(this).parent().attr('data-group-id'));
        });
        $(".btn-save-group-name").click(function(){
            var groupID = $(this).parent().attr("data-group-id");
            var groupName = $(this).parent().find(".edit-group-name").val();
            StoreDashboard.ProductGroup.save(groupID,groupName);
        });
        $(".btn-edit-group-name").click(function(){
            var groupID = $(this).parent().attr("data-group-id");
            StoreDashboard.ProductGroup.edit(groupID);
        });
    },
    delete: function(groupID){
        var confirmDelete = confirm(VividStoreStrings.areYouSure);
        if(confirmDelete == true) {
            var deleteurl = $(".group-list").attr("data-delete-url");
            $.ajax({
                url: deleteurl+"/"+groupID,
                success: function() {
                    $("li[data-group-id='"+groupID+"']").remove();
                },
                error: function(){
                    alert(VividStoreStrings.error);
                }
            });
        }
    },
    save: function(groupID,groupName){
        var saveurl = $(".group-list").attr("data-save-url");
        $.ajax({
            url: saveurl+"/"+groupID,
            data: {gName: groupName},
            type: 'post',
            success: function() {
                $("li[data-group-id='"+groupID+"']").find(".group-name").text(groupName);
                $("li[data-group-id='"+groupID+"']").find(".btn-edit-group-name,.group-name").show();
                $("li[data-group-id='"+groupID+"']").find(".edit-group-name, .btn-cancel-edit, .btn-save-group-name").attr("style","display: none");
            },
            error: function(){
                alert(VividStoreStrings.error);
            }
        });
    },
    edit: function(groupID){
        $("li['data-group-id="+groupID+"']").find(".btn-edit-group-name, .group-name").hide();
        $("li['data-group-id="+groupID+"']").find(".edit-group-name, .btn-cancel-edit, .btn-save-group-name").attr("style","display: inline-block !important");
    },
    cancel: function(groupID){
        $("li['data-group-id="+groupID+"']").find(".btn-edit-group-name, .group-name").show();
        $("li['data-group-id="+groupID+"']").find(".edit-group-name, .btn-cancel-edit, .btn-save-group-name").attr("style","display: none");
    }
}

StoreDashboard.Order = {
    init: function(){

    },
    deleteOrder: function(id){
        
    }
}

$(function(){
    StoreDashboard.ProductGroup.init();

    $("#btn-delete-order").click(function(e){
        e.preventDefault();
        var url = $(this).attr("href");
        var confirmDelete = confirm('Are You Sure?');
        if(confirmDelete == true) {
            window.location = url;
        }
    });
    $("#btn-generate-page").click(function(e){
        e.preventDefault();
        var url = $(this).attr("href");
        var pageTemplate = $("#selectPageTemplate").val();
        var confirmDelete = confirm('Just to let you know, any changes to the product will not be saved. Are you sure you want to proceed?');
        if(confirmDelete == true) {
            window.location = url+'/'+pageTemplate;        
        }
    });

    $(".add-to-panel-list .panel-heading").click(function(){
       $(this).next().toggleClass('open');
    });
    $('.add-to-panel-list li a').click(function(e){
        e.preventDefault();
        var type = $(this).attr('data-promotion');
        var handle = $(this).attr('data-handle');
        if(type=='reward-type'){
            var title = VividStoreStrings.addRewardType;
        } else if(type=='rule-type'){
            var title = VividStoreStrings.addRuleType;
        }
        var formTemplate = _.template($('#'+handle+'-'+type+'-form').html());

        $.fn.dialog.open({
            title: title,
            width: 500,
            height: 400,
            modal: true,
            element: $('<div>' + formTemplate() + '</div>'),
            open: function(){
                $('.ui-dialog').addClass('vivid-store-dialog ccm-ui');
            },
            buttons: [
                {
                    text: VividStoreStrings.add,
                    click: function () {
                        var listItemTemplate = _.template($('#promotion-list-item').html());
                        var completeFunction = $('#'+handle+'-'+type+'-form').attr('data-complete-function')
                        var content = window[completeFunction]();
                        var params = {
                            handle: handle,
                            content: content
                        }
                        type = type.replace('-type','');
                        $("#promotion-"+type+"-list").append(listItemTemplate(params));
                        $(this).dialog('close');
                        $(".add-to-panel-list .panel-body").removeClass('open');
                    }
                },
                {
                    text: VividStoreStrings.cancel,
                    click: function () {
                        $(this).dialog('close');
                    }
                }
            ]
        });
    });

});


function searchForProduct(id){
    var target = '#product-search-form-'+id;
    var inputField = $(target + " .product-search-input");
    var searchString = inputField.val();
    if(searchString.length > 0){
        $(target + " .product-search-results").addClass("active");
        $.ajax({
            type: "post",
            url: $(target).attr('data-ajax-url'),
            data: {query: searchString},
            success: function(html){
                $(target + " .results-list").html(html);
                $(target + " .product-search-results ul li").click(function(){
                    var pID = $(this).attr('data-product-id');
                    var productName = $(this).text();
                    $(target + " .product-id-field").val(pID);
                    $(target + " .product-search-results").removeClass("active");
                    $(target + " .product-search-input").val('');
                    $(target + " .selected-product").html(productName);
                });
                $("*:not(.product-search-results ul li)").click(function(){
                    $(target + " .product-search-results").removeClass("active");
                });
            }
        });
    } else {
        $(target + " .product-search-results").removeClass("active");
    }
}

function updateTaxStates(){
    var countryCode = $("#taxCountry").val();
    var selectedState = $("#savedTaxState").val();
    var stateutility = $("#settings-tax").attr("data-states-utility");
    $.ajax({
       url: stateutility,
       type: 'post',
       data: {country: countryCode, selectedState: selectedState, type: "tax"},
       success: function(states){
           $("#taxState").replaceWith(states);

           if (states.indexOf(" selected ") >= 0) {
               $("#taxState").prepend("<option value=''></option>");
           } else {
               $("#taxState").prepend("<option value='' selected='selected'></option>");
           }
       } 
    });
}
updateTaxStates();
