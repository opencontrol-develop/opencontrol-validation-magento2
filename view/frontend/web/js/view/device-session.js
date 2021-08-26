define([
    'jquery',
    'uiComponent',
    'ko'
    ], function ($, Component, ko) {
        'use strict';

        $(document).ready(function(){
            if(window.checkoutConfig.is_active){
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