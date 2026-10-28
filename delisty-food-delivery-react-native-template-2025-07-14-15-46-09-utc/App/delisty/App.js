import "./src/polyfills/interactionManager";
import React from "react";
import { useFonts } from "expo-font";

import AppNavigation from "./src/navigation/AppNavigation";

import { Rancho_400Regular } from "@expo-google-fonts/rancho";
import {
    Lato_400Regular,
    Lato_700Bold,
    Lato_900Black,
} from "@expo-google-fonts/lato";

export default function App() {
    let [fontsLoaded] = useFonts({
        Rancho_400Regular,
        Lato_400Regular,
        Lato_700Bold,
        Lato_900Black,
    });

    // On web or native, render navigation with fallback font while fonts load
    return <AppNavigation />;
}
