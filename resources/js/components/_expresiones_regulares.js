import {helpers} from 'vuelidate/lib/validators'

export const nombreproceso = helpers.regex('alpha', /^[a-zA-ZÀ-ÿ&\u00f1\u00d1\s]*['/]?[a-zA-ZÀ-ÿ&\u00f1\u00d1\s]*$/)
export const nombreentradasalida = nombreproceso
export const nombrecriterio = nombreproceso
export const nombreactividad = nombreproceso
export const txtjustificacion = helpers.regex('alpha', /^[a-zA-ZÀ-ÿ0-9&\u00f1\u00d1\s]*['/]?[a-zA-ZÀ-ÿ0-9&\;\.\,\-\:\u00f1\u00d1\s]*$/)
export const txtdescipcion = txtjustificacion

export const nombreindicador = nombreproceso
export const descripcionindicador = txtjustificacion
export const nombreobjestrategico = nombreproceso
export const nombretablero = nombreproceso
export const descripciontablero = txtjustificacion
