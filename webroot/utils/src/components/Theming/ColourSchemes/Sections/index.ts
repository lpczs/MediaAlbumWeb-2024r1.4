export type SectionProps<T> = {
  props: T;
  shadowRoot: Document | ShadowRoot;
  section: null | 'colours' | 'buttons' | 'contentStyles';
  subTheme: string;
  editable: boolean;
  contentStyle?: string;
  diff?: Partial<T>
}