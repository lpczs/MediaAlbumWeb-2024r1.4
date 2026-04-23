import i18n from 'i18next';
import {initReactI18next} from 'react-i18next';
import Backend from 'i18next-http-backend';
import LanguageDetector from 'i18next-browser-languagedetector';

declare global {
  interface Window { gLangCode: string }
}

/**
 * Custom function to load language string from taopix online
 *
 * @param   {any}     options
 * @param   {string}  url
 * @param   {any}     payload
 * @param   {any}     callback
 *
 * @return  {[type]}
 */
const loadLocales = (options: any, url: string, payload: any, callback: any) => {
  return new Promise((resolve, reject) => {
    fetch(url, {mode: 'cors'})
      .then(response => {
        if (response.status === 404) {
          reject();
        }
        response.json().then(locale => {
          return callback(null, {status: 200, data: locale});
        });
      })
      .catch(error => callback(error, {status: 404}));
  }).catch(error => callback(error, {status: 404}));
};

const getBaseUrl = (): string => {
  if ((window as any)?.location?.href) {
    let href = (window as any).location.href;
    href += (href[href.length - 1] === "/") ? "" : "/";
    return href;
  }
  return 'https://controlcentre.hq.taopix.com/';
};

i18n
  // load translation using http -> see /public/locales (i.e. https://github.com/i18next/react-i18next/tree/master/example/react/public/locales)
  // learn more: https://github.com/i18next/i18next-http-backend
  // want your translations to be loaded from a professional CDN? => https://github.com/locize/react-tutorial#step-2---use-the-locize-cdn
  .use(Backend)
  // detect user language
  // learn more: https://github.com/i18next/i18next-browser-languageDetector
  .use(LanguageDetector)
  // pass the i18n instance to react-i18next.
  .use(initReactI18next)
  // init i18next
  // for all options read: https://www.i18next.com/overview/configuration-options
  .init({
    fallbackLng: 'en',
    debug: false, // disable this in production
    ns: [
      '*',
      'AdminExperience',
      'AdminConnectors',
      'AdminTheming'
    ],
    defaultNS: '*',
    lng: window.gLangCode,
    backend: {
      loadPath: `${getBaseUrl()}api/language/{{lng}}/{{ns}}`,
      requestOptions: {
        mode: 'no-cors',
      },
      request: loadLocales,
    },
  });

export default i18n;
