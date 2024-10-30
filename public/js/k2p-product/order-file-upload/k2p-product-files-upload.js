jQuery(document).ready(function () {
    initUploadPanel();
});
jQuery(document.body).on('updated_cart_totals', function () {
    initUploadPanel();
});

var ajaxCallQueue = [];
var ajaxLock = false;


function addToCallQueue(callbackFn) {
    if (ajaxLock == false) {
        callbackFn();
        ajaxLock = true;
    } else {
        ajaxCallQueue.push(callbackFn);
    }
}

function finishCall() {
    if (ajaxCallQueue.length === 0) {
        ajaxLock = false;
        return;
    } else {
        var callbackFn = ajaxCallQueue.pop();
        callbackFn();
    }

}


function initUploadPanel() {
    jQuery('.upload_container').each(function () {
        var isUploaded = jQuery(this).data('is-uploaded');
        if (isUploaded) {
            jQuery(this).find('.upload_container_status').show();
            jQuery(this).find('.upload_container_button').hide();
        } else {
            jQuery(this).find('.upload_container_status').hide();
            jQuery(this).find('.upload_container_button').show();
            jQuery(this).find('.upload_container_button').find('.percent_value').hide();
        }
    });

    jQuery('.file_upload_remove').click(function () {
        var cartItemKey = jQuery(this).data('cart-item-key');
        var fileComponentId = jQuery(this).data('file-component-id');
        addToCallQueue(function () {
            jQuery.ajax({
                method: "POST",
                url: ajax_object.ajax_url,
                data: {
                    action: 'remove_k2p_product_upload',
                    cart_item_key: cartItemKey,
                    file_component_id: fileComponentId,
                },
                success: function (serverResponse) {
                    finishCall();
                    showUploadButton(cartItemKey, fileComponentId);
                }
            })
        });
    });

    jQuery('.cart_item_upload').fileupload({
        url: ajax_object.chunk_upload_url,
        maxChunkSize: 10000000, // 10MB
        autoUpload: false,
        add: function (e, data) {
            var uploadErrors = [];

            //var acceptFileTypes = /^image\/(gif|jpe?g|png|pdf|tiff|eps|svg|psd)$/i;
            var acceptFileTypes = /\/(gif|jpe?g|png|pdf|tiff|eps|svg|psd)$/i;
            if (data.originalFiles[0]['type'].length && !acceptFileTypes.test(data.originalFiles[0]['type'])) {
                uploadErrors.push('Not an accepted file type');
            }

            var cartItemKey = jQuery(this).data('cart-item-key');
            var fileComponentId = jQuery(this).data('file-component-id');

            if (uploadErrors.length > 0) {
                alert(uploadErrors.join("\n"));
            } else {
                showProgessBar(cartItemKey, fileComponentId, '0%');
                addToCallQueue(function () {
                    jQuery.ajax({
                        method: "POST",
                        url: ajax_object.ajax_url,
                        data: {
                            action: 'start_k2p_product_upload',
                            cart_item_key: cartItemKey,
                            file_component_id: fileComponentId,
                            filename: data.files[0].name,
                        },
                        success: function (serverResponse) {
                            finishCall();
                            serverResponse = JSON.parse(serverResponse);
                            data.formData = {upload_id: serverResponse.data.api_upload_id};
                            data.submit();
                        }
                    });
                });
            }
        },
        done: function (e, data) {
            var uploadId = data.formData.uploadId;
            var cartItemKey = jQuery(this).data('cart-item-key');
            var fileComponentId = jQuery(this).data('file-component-id');
            var containerId = "#upload_container_" + cartItemKey + "_" + fileComponentId;

            addToCallQueue(function () {
                jQuery.ajax({
                    method: "POST",
                    url: ajax_object.ajax_url,
                    data: {
                        action: 'complete_k2p_product_upload',
                        cart_item_key: cartItemKey,
                        file_component_id: fileComponentId,
                    },
                    success: function (serverResponse) {
                        finishCall();
                        serverResponse = JSON.parse(serverResponse);
                        showProgessBar(cartItemKey, fileComponentId, '');
                        showUploadStatus(cartItemKey, fileComponentId, serverResponse.data.url, serverResponse.data.filename);
                    }
                })
            });
        },
        progressall: function (event, data) {
            var percentComplete = Math.round((data.loaded / data.total) * 1000) / 10;
            percentComplete = Math.min(99, percentComplete);
            var percentVal = percentComplete + '%';

            var cartItemKey = jQuery(this).data('cart-item-key');
            var fileComponentId = jQuery(this).data('file-component-id');

            showProgessBar(cartItemKey, fileComponentId, percentVal);
        }
    });
}

function getContainerId(cartItemKey, fileComponentId) {
    return "#upload_container_" + cartItemKey + "_" + fileComponentId;
}

function showUploadButton(cartItemKey, fileComponentId) {
    jQuery(getContainerId(cartItemKey, fileComponentId)).find('.upload_container_status').hide();
    jQuery(getContainerId(cartItemKey, fileComponentId)).find('.upload_container_button').show();
    jQuery(getContainerId(cartItemKey, fileComponentId)).find('.percent_value').hide();
}

function showProgessBar(cartItemKey, fileComponentId, percentVal) {
    jQuery(getContainerId(cartItemKey, fileComponentId)).find('.percent_value').show();
    jQuery(getContainerId(cartItemKey, fileComponentId)).find('.percent_bar').css("width", percentVal);
}

function showUploadStatus(cartItemKey, fileComponentId, url, filename) {
    jQuery(getContainerId(cartItemKey, fileComponentId)).find('.upload_container_status').show();
    jQuery(getContainerId(cartItemKey, fileComponentId)).find('.upload_container_button').hide();

    jQuery(getContainerId(cartItemKey, fileComponentId)).find('.file_upload_link').attr("href", url);
    jQuery(getContainerId(cartItemKey, fileComponentId)).find('.file_upload_link').html(filename);
}