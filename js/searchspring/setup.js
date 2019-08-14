jQuery.noConflict();
(function($){

    $(function() {
        var websites;

        var configForm = $('#config_edit_form');

        var reconnectButton = $('#ss-reconnect-button');

        var usernameField = $('#ssmanager_ssmanager_setup_username');
        var passwordField = $('#ssmanager_ssmanager_setup_password');
        var authSelect = $('#ssmanager_ssmanager_setup_authentication_method');
        var loginButton = $('#ss-login-button');

        var websiteSelect = $('#ssmanager_ssmanager_setup_website');
        var feedSelect = $('#ssmanager_ssmanager_setup_feed');
        var connectButton = $('#ss-connect-button');

        var confirmConnectWindow = $('#ss-confirm-connect');
        var cancelButton = $('#ss-cancel-button');
        var confirmButton = $('#ss-confirm-button');

        var siteIdEl = $('#ssmanager_ssmanager_api_site_id');
        var feedIdEl = $('#ssmanager_ssmanager_api_feed_id');
        var secretKeyEl = $('#ssmanager_ssmanager_api_secret_key');

        var authEl = $('#ssmanager_ssmanager_api_authentication_method');

        var storeSelect = $('#ss-store-select');

        // Hide Connection Settings Block
		var apiHeadEl = $('#ssmanager_ssmanager_api-head');
		apiHeadEl.parents('.section-config').hide();

        reconnectButton.bind('click', reconnect);

        loginButton.bind('click', loginSearchSpring);
        usernameField.bind('keypress', loginEnter);
        passwordField.bind('keypress', loginEnter);

        storeSelect.bind('change', selectStore);

        function reconnect() {
            $('.ss-login-connected').hide();
            $('.ss-login-not_connected').show();
        }

        function loginEnter(ev) {
            if (ev.keyCode == 13) {
                loginSearchSpring();
                return false;
            }
            return true;
        }

        function loginSearchSpring() {
            loginButton.addClass('loading');
            $.ajax({
                url: 'https://api-beta.searchspring.net/api/manage/users/get-feeds.json',
                type: 'POST',
                dataType: 'jsonp',
                success : login,
                data : {
                    username : usernameField.val(),
                    password : passwordField.val()
                }
            });

        }

        function login(data, status, req) {
            loginButton.removeClass('loading');

            if(data.status == 'error') {
                alert(data.message);
                return;
            }

            websites = data.websites;

            $('.ss-step1').hide();
            setSiteDropdown();
            $('.ss-step2').show();
        }

        function setSiteDropdown() {

            websiteSelect.empty();
            feedSelect.empty().attr('disabled', '').unbind('change');
            disableConnect();

            var websitesLength = getObjectLength(websites);
            var option;

            if(websitesLength > 1) {
                option = $('<option />').val(0).text('Select a website');
                websiteSelect.append(option);
            }

            for(var websiteId in websites) {
                if(websites.hasOwnProperty(websiteId)) {
                    var website = websites[websiteId];
                    option = $('<option />').val(websiteId).data('siteId', website.siteId).data('secretKey', website.secretKey).text(website.name);
                    websiteSelect.append(option);
                }
            }

            websiteSelect.removeAttr('disabled').bind('change', setFeedDropdown);

            if(websitesLength == 1) {
                setFeedDropdown();
            }
        }

        function setFeedDropdown() {
            var feeds = websites[websiteSelect.val()].feeds;

            feedSelect.empty();
            disableConnect();

            if(!websites[websiteSelect.val()].secretKey) {
                alert('Secret Key not found. Please contact SearchSpring Support (support@searchspring.com)');
                return;
            }

            var feedsLength = getObjectLength(feeds);
            var option;
            if(feedsLength > 1) {
                option = $('<option />').val(0).text('Select a feed');
                feedSelect.append(option);
            }

            for(var feedId in feeds) {
                if(feeds.hasOwnProperty(feedId)) {
                    option = $('<option />').val(feedId).text(feeds[feedId]);
                    feedSelect.append(option);
                }
            }

            feedSelect.removeAttr('disabled').bind('change', enableConnect);

            if(feedsLength == 1) {
                enableConnect();
            }
        }

        function enableConnect() {
            if(authSelect.val() == 'simple') {
                connectButton.removeClass('disabled').removeAttr('disabled').bind('click', connectSettings);
            } else {
                connectButton.removeClass('disabled').removeAttr('disabled').bind('click', confirmConnect);
            }
        }

        function disableConnect() {
            connectButton.addClass('disabled').attr('disabled', '').unbind('click');
        }

        function confirmConnect() {
            confirmConnectWindow.show();
            confirmButton.bind('click', connectSettings);
            cancelButton.bind('click', cancelConnect);
        }

        function cancelConnect() {
            confirmButton.unbind('click');
            cancelButton.unbind('click');
            confirmConnectWindow.hide();
        }

        function connectSettings() {
            confirmButton.addClass('loading');
            siteIdEl.val(websites[websiteSelect.val()].siteId);
            secretKeyEl.val(websites[websiteSelect.val()].secretKey);
            feedIdEl.val(feedSelect.val());
            authEl.val(authSelect.val());

			// Send flag to setup new connection
            $('#searchspring_new_connection_fl').val('1');

            configForm.submit();
        }

        function selectStore() {
            var val = $(this).val();
            if(val != 'na') {
                location.href = val;
            }
        }

        function getObjectLength(obj) {
            var length = 0;
            for (var key in obj) {
                if (obj.hasOwnProperty(key)) length++;
            }
            return length;
        }
    });

})(jQuery);
