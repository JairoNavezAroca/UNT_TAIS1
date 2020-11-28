require('./bootstrap');
window.Vue = require('vue');

//BootstrapVue
import { BootstrapVue, IconsPlugin } from 'bootstrap-vue'
Vue.use(BootstrapVue)
Vue.use(IconsPlugin)

//File agent
import VueFileAgent from 'vue-file-agent';
import VueFileAgentStyles from 'vue-file-agent/dist/vue-file-agent.css';
Vue.use(VueFileAgent);

//Vue-search-select
//import 'vue-search-select/dist/VueSearchSelect.css';

//VueCoolSelect
import { CoolSelectPlugin } from 'vue-cool-select'
import 'vue-cool-select/dist/themes/bootstrap.css'
Vue.use(CoolSelectPlugin)

//sweetalert
import VueSweetalert2 from 'vue-sweetalert2';
Vue.use(VueSweetalert2);

//Vuelidate
import Vuelidate from 'vuelidate'
Vue.use(Vuelidate)

//Vue-loaders
import 'vue-loaders/dist/vue-loaders.css'
import VueLoaders from 'vue-loaders'
Vue.use(VueLoaders)

import _funciones from './components/_funciones.js';
Vue.mixin(_funciones);


Vue.component('example-component', require('./components/ExampleComponent.vue').default);

//Extras
Vue.component('sube-archivos', require('./components/extras/SubeArchivos.vue').default);
Vue.component('loader', require('./components/extras/Loader.vue').default);

//Usuarios
Vue.component('usuarios', require('./components/usuarios/Usuarios.vue').default);
Vue.component('usuarios-editor', require('./components/usuarios/UsuariosEditor.vue').default);

//Empresas
Vue.component('empresas', require('./components/empresas/Empresas.vue').default);
Vue.component('empresas-editor', require('./components/empresas/EmpresasEditor.vue').default);
Vue.component('unidad-negocio', require('./components/empresas/UnidadNegocio.vue').default);

//Cadena de Suministro
Vue.component('cadenas', require('./components/cadenas/Cadenas.vue').default);
Vue.component('cadenas-editor', require('./components/cadenas/CadenasEditor.vue').default);
Vue.component('cadenas-modelador', require('./components/cadenas/CadenasModelador.vue').default);
Vue.component('cadenas-proveedores-clientes', require('./components/cadenas/modelador/ProveedoresClientes.vue').default);
Vue.component('cadenas-proveedores-clientes-editor', require('./components/cadenas/modelador/ProveedoresClientesEditor.vue').default);
Vue.component('cadenas-previsualizar', require('./components/cadenas/modelador/Previsualizar.vue').default);
Vue.component('cadenas-versiones', require('./components/cadenas/modelador/Versiones.vue').default);

//Mapa de Procesos
Vue.component('mapaprocesos', require('./components/mapaprocesos/MapaProcesos.vue').default);
Vue.component('mapaprocesos-editor', require('./components/mapaprocesos/MapaProcesosEditor.vue').default);
Vue.component('mapaprocesos-modelador', require('./components/mapaprocesos/MapaProcesosModelador.vue').default);
Vue.component('mapaprocesos-mapaprocesos', require('./components/mapaprocesos/mapaprocesos/_MapaProcesos.vue').default);
Vue.component('mapaprocesos-mapaprocesos-entradasalida', require('./components/mapaprocesos/mapaprocesos/EntradaSalida.vue').default);
Vue.component('mapaprocesos-mapaprocesos-entradasalida-editor', require('./components/mapaprocesos/mapaprocesos/EntradaSalidaEditor.vue').default);
Vue.component('mapaprocesos-mapaprocesos-procesos', require('./components/mapaprocesos/mapaprocesos/Procesos.vue').default);
Vue.component('mapaprocesos-mapaprocesos-procesos-editor', require('./components/mapaprocesos/mapaprocesos/ProcesosEditor.vue').default);
Vue.component('mapaprocesos-mapaprocesos-previsualizar', require('./components/mapaprocesos/mapaprocesos/Previsualizar.vue').default);
Vue.component('mapaprocesos-mapaprocesos-previsualizar-relacionar', require('./components/mapaprocesos/mapaprocesos/PrevisualizarRelacionar.vue').default);
Vue.component('mapaprocesos-matrizpriorizacion', require('./components/mapaprocesos/matrizpriorizacion/_MatrizPriorizacion.vue').default);
Vue.component('mapaprocesos-matrizpriorizacion-criterios', require('./components/mapaprocesos/matrizpriorizacion/Criterios.vue').default);
Vue.component('mapaprocesos-matrizpriorizacion-matrizpriorizacion', require('./components/mapaprocesos/matrizpriorizacion/MatrizPriorizacion.vue').default);
Vue.component('mapaprocesos-caracterizacion', require('./components/mapaprocesos/caracterizacion/_Caracterizacion.vue').default);
Vue.component('mapaprocesos-diagramaflujo', require('./components/mapaprocesos/diagramaflujo/_DiagramaFlujo.vue').default);
Vue.component('mapaprocesos-diagramaseguimiento', require('./components/mapaprocesos/diagramaseguimiento/_DiagramaSeguimiento.vue').default);

//Mapa de Procesos pt2
Vue.component('mapaprocesos-indicadoresdesempenio', require('./components/mapaprocesos/indicadoresdesempenio/_IndicadorDesempenio.vue').default);
Vue.component('mapaprocesos-indicadoresdesempenio-editor', require('./components/mapaprocesos/indicadoresdesempenio/IndicadorDesempenioEditor.vue').default);
Vue.component('mapaprocesos-mapaestrategico', require('./components/mapaprocesos/mapaestrategico/_MapaEstrategico.vue').default);
Vue.component('mapaprocesos-mapaestrategico-editor', require('./components/mapaprocesos/mapaestrategico/MapaEstrategicoEditor.vue').default);
Vue.component('mapaprocesos-mapaestrategico-grafico', require('./components/mapaprocesos/mapaestrategico/MapaEstrategicoGrafico.vue').default);
Vue.component('mapaprocesos-tablerocontrol', require('./components/mapaprocesos/tablerocontrol/_TableroControl.vue').default);
Vue.component('mapaprocesos-tablerocontrol-editor', require('./components/mapaprocesos/tablerocontrol/TableroControlEditor.vue').default);
Vue.component('mapaprocesos-tablerocontrol-datafuente', require('./components/mapaprocesos/tablerocontrol/TableroControlDataFuente.vue').default);




/*
vue-file-agent --save Subir archivos: https://safrazik.com/vue-file-agent/
vue-sweetalert2: https://www.npmjs.com/package/vue-sweetalert2
bootstrap-vue bootstrap: https://bootstrap-vue.js.org/docs/components/table/
vue-search-select: https://www.npmjs.com/package/vue-search-select
vuelidate:
vue-cool-select https://github.com/iliyaZelenko/vue-cool-select //en tais
----loader----
https://github.com/Hokid/vue-loaders
https://github.com/STUkh/vue-promise-btn
*/

const app = new Vue({
    el: '#app'
});
