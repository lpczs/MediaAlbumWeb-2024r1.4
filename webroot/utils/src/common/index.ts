import classNames from "classnames";
import { Selector, Sizes, States, ThemeButtons, ThemeColours, ThemeContentStyles, ThemeProps, ThemeSection } from "../types";
import { ColourScheme, ThemeType } from "../components/Theming/Context/ThemeContext";
import { OwnerType } from "../Enums";

export const ucfirst = (str: string) => {
  return str.charAt(0).toLocaleUpperCase() + str.slice(1);
};

export const deCamelCase = (str: string): string => {
  return str.replace(/([a-zA-Z])(?=[A-Z])/g, '$1 ').trimEnd().toLocaleLowerCase();
}

export const capitalise = (str: string, delimiter = ' '): string => {
  return str.split(delimiter).map(part => ucfirst(part)).join(delimiter);
}

export const formatTitle = (str: string, delimiter = ' '): string => {
  return capitalise(deCamelCase(str)).split('-').map(part => capitalise(part)).join(' ');
}

export const addDefaultStyle = (rules: Array<any>, scheme: ColourScheme, shadowRoot: Document | ShadowRoot): void => {
  let styleEl: HTMLStyleElement;
  if (shadowRoot.querySelector('.tpx-theme-default')) {
    styleEl = shadowRoot.querySelector('.tpx-theme-default')
  }
  else {
    styleEl = document.createElement('style');
    styleEl.setAttribute('id', scheme.hash.toLocaleLowerCase());
    styleEl.setAttribute('type', 'text/css');
    styleEl.setAttribute('class', 'tpx-theme-default');
  }

  // Append the style element
  shadowRoot?.appendChild(styleEl);

  applySheet(rules, styleEl);
}

export const addStylesheetRules = (rules: Array<any>, scheme: ColourScheme, shadowRoot: Document | ShadowRoot): void => {
  if (shadowRoot.querySelector('.tpx-theme-custom')) {
    shadowRoot.removeChild(shadowRoot.querySelector('.tpx-theme-custom') as HTMLElement);
  }

  // if we are the system theme, bail
  if (OwnerType.System === scheme.type) {
    return void 0;
  }

  const styleEl = document.createElement('style');
  styleEl.setAttribute('id', scheme.hash.toLocaleLowerCase());
  styleEl.setAttribute('type', 'text/css');
  styleEl.setAttribute('class', 'tpx-theme-custom');

  // Append the style element
  shadowRoot?.appendChild(styleEl);

  // Grab style element's sheet
  applySheet(rules, styleEl);
};

export const applySheet = (rules: Array<any>, el: HTMLStyleElement) => {
   // Grab style element's sheet
   const styleSheet = el.sheet;

   for (let i = 0; i < rules.length; i++) {
     let j = 1;
     let rule = rules[i];
     const selector = rule[0];
     let propStr = '';
     // If the second argument of a rule is an array of arrays, correct our variables.
     if (Array.isArray(rule[1][0])) {
       rule = rule[1];
       j = 0;
     }
 
     for (let pl = rule.length; j < pl; j++) {
       const prop = rule[j];
       if ("unset" !==  prop[1]) {
         propStr += `${prop[0]}: ${prop[1]}${prop[2] ? ' !important' : ''};\n`;
       }
     }
     // Insert CSS Rule
     styleSheet?.insertRule(`${selector} {${propStr}}`, styleSheet.cssRules.length);
   }
}

export const convertTheme = (data: ThemeProps) => {
  // create the class name
  const name = '.tpx-theme-main';
  const result: any[] = [];

  // convert the main variables
  const colourConverter = (colours: ThemeColours): string[] => {
    const mainStyles: any[] = [];
    for (const key in colours) {
      if (['border-width', 'rounded-corners'].includes(key)) {
        const selector = 'border-width' === key ? 'border-size' : 'corner-size';
        const value = colours[key as keyof ThemeColours];
        mainStyles.push([`--tds-theme-${selector}`, `${"unset" === value ? value: `${value}px`}`]);
      } else {
        mainStyles.push([`--tds-theme-${key}-colour`, colours[key as keyof ThemeColours]]);
      }
    }
    return mainStyles;
  };

  // convert all the button styles
  const buttonConverter = (buttons: ThemeButtons, className?: string): string[] => {
    const res: any[] = [];
    const map: Record<string, any[]> = {};
    for (const key in buttons) {
      const vars: any[] = [];
      for (const prop in buttons[key as keyof ThemeButtons]) {
        // add the sizing properties (if they exist)
        if (buttons[key as keyof ThemeButtons]?.sizing) {
          const sizing = buttons[key as keyof ThemeButtons]?.sizing;
          for (const part in sizing) {
            const value = sizing[part as keyof Sizes];
            vars.push([`--tds-button-${part}-size`, `${"unset" === value ? value: `${value}px`}`]);
          }
        }
        for (const state in buttons[key as keyof ThemeButtons][prop as keyof Omit<Selector, 'sizing'>]) {
          if ('sizing' === prop) {
            continue;
          }
          const value = buttons[key as keyof ThemeButtons][prop as keyof Omit<Selector, 'sizing'>][state as keyof States];
          vars.push([`--tds-button-${prop}-colour-${state}`, value]);
        }
      }
      map[`${className !== undefined ? className : ''} .tds-button-${key}`] = [...vars];
    }
    for (const cls in map) {
      res.push([`${name} ${cls}`, ...map[cls]]);
    }
    return res;
  };

  // convert all the content styles
  const contentStyleConverter = (contentStyles: ThemeContentStyles, themeName?: string): string[] => {
    const out: any[] = [];
    for (const key in contentStyles) {
      for (const prop in contentStyles[key]) {
        if ('colours' === prop) {
          const res = colourConverter(contentStyles[key][prop] as ThemeColours);
          const base= `${name} ${themeName ? themeName: ''}`
          out.push([`${base} .tds-theme-${key}`, ...res]);
        }

        if ('buttons' === prop) {
          const res = buttonConverter(
            contentStyles[key][prop] as ThemeButtons,
            `${themeName ? themeName: ''} .tds-theme-${key}`
          );
          out.push(...res);
        }
      }
    }
    return out;
  };

  const subThemeConverter = (themeName: string, subTheme: ThemeSection) => {
    const out: any[] = [];
    for (const prop in subTheme) {
      if ('colours' === prop) {
        const res = colourConverter(subTheme[prop] as ThemeColours);
        if ('main' === themeName) {
          out.push([`${name}`, ...res]);
        } else {
          out.push([`${name} .tpx-theme-${themeName}`, ...res]);
        }
      }

      else if ('buttons' === prop) {
        let res: string[];
        if ('main' === themeName) {
          res = buttonConverter(subTheme[prop] as ThemeButtons);
        } else {
          res = buttonConverter(subTheme[prop] as ThemeButtons, `.tpx-theme-${themeName}`);
        }
        out.push(...res);
      }

      else if ('contentStyles' === prop) {
        if ('main' === themeName) {
          out.push(...contentStyleConverter(subTheme[prop] as ThemeContentStyles));
        } else {
          out.push(...contentStyleConverter(subTheme[prop] as ThemeContentStyles, `.tpx-theme-${themeName}`));
        }
      }
    }
    return out;
  };

  Object.keys(data).forEach(subTheme => {
    result.push(...subThemeConverter(subTheme, data[subTheme as keyof ThemeProps]));
  });

  return result;
};

/**
 * getCurrentLocaleString
 *
 * @param sourceString string // text to split
 * @param useFirstAsFallback boolean // true -> if the language is not found and the iteration find will be return
 */
export const getCurrentLocaleString = (sourceString: string, useFirstAsFallback: boolean): string => {
  if (!sourceString) return '';
  let result: string = null;
  let langCode: string;
  const splitStrings = sourceString.split("<p>");

  for (let i = 0, iLength = splitStrings.length; i < iLength; i++) {
    let item = splitStrings[i];
    const spacePos = item.indexOf(' ');
    if (spacePos > 0) {
      langCode = item.substring(0, spacePos);
      item = item.slice(spacePos + 1);
      if (langCode == window.gLangCode) {
        result = item;
      } else if (i === 0 && useFirstAsFallback) {
        result = item;
      }
    }

    // check for english as a last resort
    if (result === '' && langCode === 'en') {
      result = item;
    }
  }
  return result;
};

/**
 * Locates the first scrollable parent of the element provided.
 *
 * @param node On first pass, this is the element to start from. Following calls will be the node's parent.
 * @returns The scrollable element, or null if none found.
 */
export const getScrollParent = (node: HTMLElement | null): HTMLElement | null => {
  if (!(node instanceof HTMLElement)) {
    return null;
  }

  const computed = window.getComputedStyle(node);

  if (
    (!['visible', 'hidden'].includes(computed.overflowY) &&
      node.scrollHeight > node.clientHeight) ||
    (!['visible', 'hidden'].includes(computed.overflowX) && node.scrollWidth > node.clientWidth)
  ) {
    return node;
  } else {
    return getScrollParent(node.parentNode as HTMLElement);
  }
};
