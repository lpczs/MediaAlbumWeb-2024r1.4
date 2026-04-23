import { AssignmentColumnId, AssignmentType, ExperienceSystemType, ExperienceType, ProductType } from './Enums';
import { LogoutResponse } from './Interfaces';
import { ThemeType } from './components/Theming/Context/ThemeContext';

export type LiveSearch = {
  results: Array<any>;
} & LogoutResponse;

export type Experience = {
  id: number;
  experienceType: ExperienceType;
  name: string;
  productType: ProductType;
  retroPrint: boolean;
  data: Object;
  dataLength: number;
  assignment: Array<string>;
  isdirty?: boolean;
  systemType: ExperienceSystemType;
  code: string;
};

export type ExperienceServerResponse = {
  data: Array<Experience>;
  schema: Object;
  baseExperience: Array<Record<number, Object>>;
  features: Features;
} & LogoutResponse;

export type ExperienceSaveServerResponse = {
  data: {
    experienceid: number;
    assignment: number;
  };
} & LogoutResponse;

export type Selections = {
  keys: Array<string>;
  templates: Array<Template>;
  assignmentType: number;
};

export type Template = {
  templateId: number;
  type: number;
  productType: number;
  retroPrint: boolean;
};

export type ProductTypeData = {
  type: number;
  retroPrint: boolean;
};

export type Collection = Record<
  string,
  {
    collectionName: string;
    products?: Product;
  }
>;

export type Product = Record<
  string,
  {
    code: string;
    name: string;
  }
>;

export type ExperienceAssignment = Record<
  string,
  {
    id: number;
    templateId: number;
    objectType: number;
    productCode: string;
    theKey: string;
    productType: number;
    retroPrint: boolean;
  }
>;

export type ExperienceOverviewServerResponse = {
  collections: Collection;
  brands: Brand;
  templates: Record<number, TemplateSelect>;
  assignment: Record<number, ExperienceAssignment>;
  success?: Boolean;
  page: number;
  features: Features;
  themes: Record<number, ThemeType>;
  totalRecords: number
};

export type Brand = Record<
  string,
  {
    code: string;
    name: string;
    licenseKeys: LicenseKey;
  }
>;

export type LicenseKey = Record<
  string,
  {
    code: string;
    name: string;
  }
>;

export type TemplateSelect = Record<
  number,
  {
    label: string;
    value: string;
    systemType: number;
    productType: number;
    retroPrint: boolean;
  }
>;

export type ExperienceError = {
  dataPath: string;
  message: string;
};

export type ExperienceInputProps = {
  item: any;
  experienceId: number;
  dataPath: string;
  dependenciesControl: Array<ConditionControl>;
  theKey: string;
  parentKey: string;
  value: string | boolean | number;
  changeEvent: (value: boolean, event: any, datapath: string, dependenciesControl: Array<ConditionControl>) => void;
  className?: string;
  errorArray?: { [dataPath: string]: ExperienceError };
  currentKey?: string;
  componentMountPoint?: Element;
  disabled: {disabled: boolean, disabledText: string};
  onSetErrorArray: Function;
  isDisabled: Function;
  features: Features;
  productType: ProductType;
  retroPrint: boolean;
  systemType: ExperienceSystemType;
};

export type ConfirmMessagePositiveFunction = {
  function: Function;
  param: Array<ExperienceType | Experience>;
};

export type ConditionControl = {
  keys: Array<string>;
  action: Array<ConditionActions>;
  helpText?: string;
  productType?: number;
  retroPrint?: boolean;
};

export type ConditionActions = {
  value: string;
  alternateSelection?: {
    from: string;
    to: string;
  };
  parentValue: Array<string>;
};

export type ConfirmMessage = {
  message: string;
  positiveFunction: ConfirmMessagePositiveFunction;
  negativeLabel: string;
};
/* Theme Types */
export type SubTheme = 'main' | 'header' | 'workarea' | 'toolpaletteClosed' | 'toolpaletteOpen' | 'toolpaletteContent';
export type ThemeProps = Record<SubTheme, ThemeSection>;
export type ThemeSection = {
  colours?: ThemeColours;
  buttons?: ThemeButtons;
  contentStyles?: ThemeContentStyles;
};

export type ThemeButtons = {
  primary?: Selector;
  secondary?: Selector;
  tertiary?: Selector;
  negative?: Selector;
  transparent?: Selector;
  accented?: Selector;
  plain?: Selector;
  tab?: Selector;
  imageOverlay?: Selector;
  boxless?: Selector;
};

export type Selector = {
  text?: States;
  icon?: States;
  background?: States;
  border?: States;
  accent?: States;
  sizing?: Sizes;
};

export type States = {
  default: string;
  hover: string;
  pressed: string;
  disabled: string;
};

export type Sizes = {
  accent?: string;
  border?: string;
};

export type ThemeColours = {
  background?: string;
  border?: string;
  text?: string;
  heading?: string;
  link?: string;
  accent?: string;
  placeholder?: string;
  overlay?: string;
};

export type ThemeContentStyles = Record<string, ThemeSection>;
export type ThemeSubThemes = Record<string, ThemeSection>;

export type EditorDialogState<T> = {
  open: boolean;
  name: string;
  props: T;
};

export type AssignmentColumn = {
  id: AssignmentColumnId;
  label: string;
  typeLabel?: string;
  type: AssignmentTypeData;
  productType: ProductTypeData;
};

export type AssignmentTypeData = {
  type: AssignmentType;
  subType: number;
};

export type AssignmentColumnData = {
  columns: AssignmentColumn[];
  selected: AssignmentColumnId[];
  recentlyAddedId?: AssignmentColumnId;
};

export type Features = { 
  ai: boolean; 
  retroPrints: boolean;
  scaleBeforeUpload: boolean;
}