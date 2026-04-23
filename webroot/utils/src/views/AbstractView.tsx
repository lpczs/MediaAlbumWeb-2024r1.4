import { getTheme } from '@taopix/taopix-design-system';

export type ViewOptions = {
  documentRoot: Document | ShadowRoot;
  container: HTMLElement | string;
  sessionRef: number;
  type?: number;
  userId: string;
};

export default class AbstractView {
  public widget: HTMLDivElement;
  constructor(public route: any, public options: ViewOptions) {}

  public hide() {
    this.widget.remove();
  }

  public display() {
    const { route, options } = this;
    const body = document.body;

    const container =
      options.container !== undefined
        ? typeof options.container === 'string'
          ? document.querySelector(options.container) ?? undefined
          : options.container
        : undefined;

    if (container) {
      let reactPanel = document.querySelector('[data-faction="' + route + '"]');

      if (reactPanel) {
        reactPanel.remove();
      }
    }
    this.widget = document.createElement('div');
    this.widget.dataset.faction = route;
    this.widget.classList.add(
      ...getTheme('cc-theme-main', true, false, false).split(' '),
      'flex',
      'flex-1',
      'overflow-auto'
    );
    (container ?? body).appendChild(this.widget);
  }
}
