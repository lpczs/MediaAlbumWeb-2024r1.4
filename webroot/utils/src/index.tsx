import ExperienceEditingView from "./views/ExperienceEditingView";
import ExperienceThemeView from "./views/ExperienceThemeView";
import ExperienceOverview from "./views/ExperienceOverview";
import { BaseViewOptions } from "./views";
import './../../css/tailwind.css';
import '@taopix/taopix-design-system/dist/taopix.css';
import './css/theme.css';
import './i18n/i18n'; 


declare global {
    interface Window {
        Router:any
    }
}
const routes: Record<string, any> = {
    'AdminExperienceEditing': ExperienceEditingView,
    'AdminExperienceTheme': ExperienceThemeView,
    'AdminExperienceEditingOverview': ExperienceOverview
  }

  export const Router = () =>{
    return {
        get: (route: any, options: BaseViewOptions) => {
          if (routes[route]) {
            return (new routes[route](route, options));
          }
        return null;
      }
    }
  }

  window.Router = Router;