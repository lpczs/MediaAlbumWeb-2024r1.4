import Ajv from "ajv"
import { ColourScheme } from "../../components/Theming/Context/ThemeContext";
import schema from '../../utils/Theming/schema.json';

type ValidatorFn = (scheme: ColourScheme) => Promise<void>;

/**
 * Validate the scheme name
 *
 * @param   {ColourScheme}  scheme
 *
 * @return  {Promise<void>}
 */
const schemeNameValidator = (scheme: ColourScheme): Promise<void> => {
  return new Promise((resolve, reject) => {
    if ('' === scheme.name) {
      reject('Theme name cannot be blank');
    }
    resolve(void 0);
  })
}

/**
 * Validate the scheme payload
 *
 * @param   {ColourScheme}  scheme
 *
 * @return  {Promise<void>}
 */
const schemeDataValidator = (scheme: ColourScheme): Promise<void> => {
  return new Promise((resolve, reject) => {
    const {data} = scheme;

    // check if it's a valid object
    if ('object' !== typeof data) {
      reject('not an object');
    }

    // check if it's valid json
    if (!JSON.parse(JSON.stringify(data))) {
      reject('not a valid json object');
    }

    // if the data is an empty object, allow it through
    if (0 === Object.keys(data).length) {
      resolve(void 0);
    }

    // perform some schema validation
    const ajv = new Ajv();
    const validator = ajv.compile(schema);
    if (!validator(data)) {
      reject(validator.errors);
    }

    resolve(void 0);
  })
}

/**
 * Validator function wrapper. Executes all validator functions passed
 *
 * @param   {ColourScheme}        scheme
 * @param   {Array<ValidatorFn>}  validators
 *
 * @return  {Promise<void>[]}
 */
const schemeValidator = (scheme: ColourScheme, validators: Array<ValidatorFn>): Promise<void[]> => {
  return Promise.all([
    ...validators.map(validator => validator(scheme))
  ]);
}


/**
 * Validates a scheme name & data properties
 *
 * @param   {ColourScheme}  scheme
 *
 * @return  {Promise<void>[]}
 */
export default (scheme: ColourScheme): Promise<void[]> => {
  return schemeValidator(scheme, [
    schemeNameValidator, 
    schemeDataValidator
  ]);
}