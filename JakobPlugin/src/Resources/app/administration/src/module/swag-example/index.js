// <plugin root>/src/Resources/app/administration/src/module/swag-example/index.js
import { notification }  from '@shopware-ag/meteor-admin-sdk';
import { ui, location } from '@shopware-ag/meteor-admin-sdk';
import deDE from './snippet/de-DE.json';
import enGB from './snippet/en-GB.json';
import './page/jakob-overview';

const { Component, Module } = Shopware;


console.log('MAIN JS LOADED')

Module.register('your-plugin', {
    type: 'plugin',
    name: 'YourPlugin',
    title: 'Your Plugin',
    description: 'Custom menu item from plugin',
    color: '#9AA8B5',
    icon: 'default-action-settings',

    routes: {
        index: {
            component: 'your-page',
            path: 'index'
        }
    },

    navigation: [{
        label: 'Your Plugin',
        path: 'your.plugin.index',
        icon: 'default-action-settings',
        position: 100
    }]
});