import { useMemo } from "react";
import { useTheming } from "../Context/ThemeContext";

const useSelectedScheme = () => {
  const {state: {colourSchemes, selectedSchemeId}} = useTheming();

  return useMemo(() => {
    if (!selectedSchemeId) {
      return null;
    }
    return colourSchemes[selectedSchemeId];
  }, [colourSchemes, selectedSchemeId])
}

export default useSelectedScheme;