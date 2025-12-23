// Import admin module

import './module/pricetest';
import './module/discount-tags';


import 'bootstrap/dist/css/bootstrap.css';
import 'bootstrap-vue-next/dist/bootstrap-vue-next.css';
/*Vue.use(BootstrapVue)
Vue.use(IconsPlugin)*/

/*
sw.notification.dispatch({
  title: 'My first notification',
  message: 'This was really easy to do'
})
if (!location.isIframe()) {
  const myLocationId = 'my-example-location-id';

  // Create a new tab entry
  ui.tabs('sw-product-detail').addTabItem({
      label: 'Example tab',
      componentSectionId: 'example-product-detail-tab-content'
  })

  // Add a new card to the tab content which renders a location
  ui.componentSection.add({
      component: 'card',
      positionId: 'example-product-detail-tab-content',
      props: {
          title: 'Component section example',
          locationId: myLocationId
      }
  })

  // Register your component which should be rendered inside the location
  Shopware.Component.register('your-component-name', {
    // your component
  })

  // Add the component name to the specific location
  Shopware.State.commit('sdkLocation/addLocation', {
      locationId: myLocationId,
      componentName: 'your-component-name'
  })
}


if (sw.location.isIframe() && !window.parent.__Cypress__) {

  sw.ui.componentSection.add({
    component: 'card',
    positionId: 'sw-chart-card__before',
    props: {
      title: 'Meteor Admin SDK',
      subtitle: 'Welcome to the example',
      locationId: 'ex-chart-card-before'
    }
  });

  sw.ui.menu.addMenuItem({
    label: 'Meteor Admin SDK example',
    locationId: 'ex-meteor-admin-sdk-example-module',
    displaySearchBar: true,
  })

  sw.ui.tabs('sw-product-detail' /!* The positionId of the tab bar*!/).addTabItem({
    label: 'Example',
    componentSectionId: 'ex-product-extension-example-page',
  })
  sw.ui.componentSection.add({
    component: 'card',
    positionId: 'ex-product-extension-example-page',
    props: {
      title: 'Data handling examples',
      subtitle: 'Test the data handling capabilities of the Meteor Admin SDK',
      locationId: 'ex-product-extension-example-data'
    }
  });

  sw.ui.componentSection.add({
    component: 'card',
    positionId: 'ex-product-extension-example-page',
    props: {
      title: 'iFrame resize example',
      subtitle: 'Test the resize capabilities of the iFrame',
      locationId: 'ex-product-extension-example-resize'
    }
  });
}*/
