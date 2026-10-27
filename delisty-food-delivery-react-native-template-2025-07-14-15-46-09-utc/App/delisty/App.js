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
    let [fontsLoaded, error] = useFonts({
        Rancho_400Regular,
        Lato_400Regular,
        Lato_700Bold,
        Lato_900Black,
    });

    if (fontsLoaded) {
        return <AppNavigation />;
    } else {
        return error;
    }
}
