import React, { Suspense } from 'react';
import { ManageInterface } from "../components/Experience/ManageInterface";
import AbstractView, { ViewOptions } from "./AbstractView";
import { createRoot } from 'react-dom/client';
import ErrorFallback from '../components/ErrorBoundary/ErrorFallback';
import { ErrorBoundary } from 'react-error-boundary';

export default class ExperienceEditingView extends AbstractView {

    constructor (route: any, options: ViewOptions) {
        super(route, options);
    }

    public display() {
      const {options} = this;
      super.display();

      const root = createRoot(this.widget);
      root.render(
      <ErrorBoundary FallbackComponent={ErrorFallback} onError={this.onError}>
        <Suspense>
          <ManageInterface type={options.type} sessionRef={options.sessionRef} documentRoot={options.documentRoot} />
        </Suspense>
      </ErrorBoundary>
      )
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
