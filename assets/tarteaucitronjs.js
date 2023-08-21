tarteaucitron.init({
    "privacyUrl": "/fr/politique-de-confidentialite", /* Privacy policy url */
    "bodyPosition": "bottom", /* or top to bring it as first element for accessibility */
    "hashtag": "#tarteaucitron", /* Open the panel with this hashtag */
    "cookieName": "tarteaucitron", /* Cookie name */
    "orientation": "bottom", /* Banner position (top - bottom) */
    "groupServices": false, /* Group services by category */
    "showAlertSmall": false, /* Show the small banner on bottom right */
    "cookieslist": false, /* Show the cookie list */
    "closePopup": true, /* Show a close X on the banner */
    "showIcon": false, /* Show cookie icon to manage cookies */
    "iconPosition": "BottomRight", /* BottomRight, BottomLeft, TopRight and TopLeft */
    "adblocker": false, /* Show a Warning if an adblocker is detected */
    "DenyAllCta" : true, /* Show the deny all button */
    "AcceptAllCta" : true, /* Show the accept all button when highPrivacy on */
    "highPrivacy": true, /* HIGHLY RECOMMANDED Disable auto consent */
    "handleBrowserDNTRequest": false, /* If Do Not Track == 1, disallow all */
    "removeCredit": false, /* Remove credit link */
    "moreInfoLink": true, /* Show more info link */
    "readmoreLink": "", /* Change the default readmore link */
    "mandatory": true, /* Show a message about mandatory cookies */
    "mandatoryCta": true /* Show the disabled accept button when mandatory on */
});

function cookiesInit(){
    // Google Analytics
    tarteaucitron.user.gtagUa = '';
    tarteaucitron.user.gtagMore = function () { /* add here your optionnal gtag() */ };
    (tarteaucitron.job = tarteaucitron.job || []).push('gtag');

    // Matomo
    tarteaucitron.user.matomoId = '';
    tarteaucitron.user.matomoHost = '';
    (tarteaucitron.job = tarteaucitron.job || []).push('matomo');

    // Google Maps
    tarteaucitron.user.googlemapsKey = '';
    (tarteaucitron.job = tarteaucitron.job || []).push('googlemaps');

    // Google Tag Manager
    tarteaucitron.user.googletagmanagerId = '';
    (tarteaucitron.job = tarteaucitron.job || []).push('googletagmanager');
    
    // Google Recaptcha
    tarteaucitron.user.recaptchaapi = '';
    (tarteaucitron.job = tarteaucitron.job || []).push('recaptcha');

    // Facebook Pixel
    tarteaucitron.user.facebookpixelId = 'YOUR-ID'; 
    tarteaucitron.user.facebookpixelMore = function () { /* add here your optionnal facebook pixel function */ };
    (tarteaucitron.job = tarteaucitron.job || []).push('facebookpixel');
    
    // Google Ads
    tarteaucitron.user.googleadsId = '';
    (tarteaucitron.job = tarteaucitron.job || []).push('googleads');

    // Facebook
    (tarteaucitron.job = tarteaucitron.job || []).push('facebook');

    // Instagram
    (tarteaucitron.job = tarteaucitron.job || []).push('instagram');

    // Youtube
    (tarteaucitron.job = tarteaucitron.job || []).push('youtube');
    
    // Rechargement après AJAX
    tarteaucitron.triggerJobsAfterAjaxCall();
}

cookiesInit();

document.addEventListener('swup:contentReplaced', function(){
    cookiesInit();
});