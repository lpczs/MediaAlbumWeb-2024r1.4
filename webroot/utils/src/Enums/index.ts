export enum ExperienceType {
  FULL = 0,
  SETTINGS = 1,
  WIZARD = 2,
  EDITOR = 3
}

export enum SettingsDataType {
  Checkbox = 'checkbox',
  Radio = 'radio',
  Section = 'section',
  Number = 'number',
  Text = 'text'
}

export enum ColourTypes {
  Background,
  Borders,
  Text,
  Headings,
  Accents
}

export enum ButtonTypes {
  Primary,
  Secondary,
  Tertiary,
  Negative,
  Transparent,
  Accented,
  Plain,
  Tab,
  ImageOverlay
}

export enum ContentStyleTypes {
  Standard,
  Primary,
  Secondary,
  Tertiary,
  Negative,
  Contained,
  Prominent,
  Information,
  Warning,
  Critical,
  Tip,
  Plain,
  Overlay,
  Transparent,
  PriceType1,
  PriceType2,
  Notification,
}

export enum OwnerType {
  System,
  User
}

export enum ProductType {
  Any = -1,
  PhotoBook = 0,
  ProofBook = 1, 
  PhotoPrints = 2,
  Calendar = 3
}

export enum ExperienceAssignMode {
  Product = 0,
  BrandAndKey = 1
}

export enum ExperienceSystemType {
  LEGACY = 0,
  SYSTEM = 1,
  CUSTOM = 2
}

export enum AssignmentType {
  Experience = 0,
  Theme = 1
}

export enum AssignmentColumnId {
  Settings,
  PhotobookDesignAssistant,
  RetroPrintDesignAssistant,
  CalendarDesignAssistant, 
  PhotobookEditor, 
  RetroPrintEditor, 
  CalendarEditor,
  Theme
}