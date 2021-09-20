define([
    'jquery',
    'uiComponent',
    'ko'
    ], function ($, Component, ko) {
        'use strict';

        $(document).ready(function(){
            const isActive = (window.checkoutConfig.is_active === '1');
            if(isActive){
                const urlDevice = window.checkoutConfig.url_device;
                console.log(urlDevice);
                document.body.insertAdjacentHTML("beforeend", "<iframe style='width:0;height:0;border:0;border:none;' src='"+urlDevice+"'></iframe>");
            }
        });

        return Component.extend({
            initialize: function () {
                this._super();
            }
        });
    }
);