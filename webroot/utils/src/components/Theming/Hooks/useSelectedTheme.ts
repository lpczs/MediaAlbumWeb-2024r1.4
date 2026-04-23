import { useMemo } from "react";
import { useTheming } from "../Context/ThemeContext";

const useSelectedTheme = () => {
  const {state: {themes, selectedThemeId}} = useTheming();

  return useMemo(() => {
    if (!selectedThemeId) {
      return null;
    }
    return themes[selectedThemeId];
  }, [themes, selectedThemeId])
}

export default useSelectedTheme;