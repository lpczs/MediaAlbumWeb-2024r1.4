import React, { Suspense } from 'react';
import { ThemeListProvider } from "../components/Theming/Context/ThemeContext";
import AbstractView, { ViewOptions } from "./AbstractView";
import { createRoot } from 'react-dom/client';
import { ErrorBoundary } from 'react-error-boundary';
import ErrorFallback from '../components/ErrorBoundary/ErrorFallback';
import { ColourEditorProvider } from '../components/Theming/Context/ColourEditorContext';
import { IssueProvider } from '../components/Theming/Context/IssuesContext';
import App from '../components/Theming/App';

export default class ExperienceThemeView extends AbstractView {

  constructor (route: any, options: ViewOptions) {
      super(route, options);
  }

  public display() {
    const {options} = this;
    super.display();

    const root = createRoot(this.widget);
    root.render(
      <ErrorBoundary FallbackComponent={ErrorFallback} onError={this.onError}>
        <ThemeListProvider>
          <Suspense>
            <IssueProvider documentRoot={options.documentRoot}>
              <ColourEditorProvider documentRoot={options.documentRoot}>
                <App documentRoot={options.documentRoot} />
              </ColourEditorProvider>
            </IssueProvider>
          </Suspense>
        </ThemeListProvider>
      </ErrorBoundary>
    );
  }

  private onError = (error: Error, info: { componentStack: string }) => {
    console.error(error.message);
  };

  public hide() {
    super.hide();
    const controller = new AbortController();
    controller.abort(); 
  }
}