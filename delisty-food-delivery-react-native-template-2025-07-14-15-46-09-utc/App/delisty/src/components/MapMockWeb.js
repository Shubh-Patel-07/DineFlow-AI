import React from "react";
import { View, Text } from "react-native";

export const Marker = ({ children }) => <View>{children}</View>;
export const Polyline = () => null;
export const PROVIDER_GOOGLE = "google";

const MapView = ({ children, style }) => {
    return (
        <View
            style={[
                {
                    backgroundColor: "#e8f0fe",
                    justifyContent: "center",
                    alignItems: "center",
                    padding: 20,
                    borderRadius: 16,
                },
                style,
            ]}
        >
            <Text style={{ color: "#1a73e8", fontWeight: "600", fontSize: 16 }}>
                📍 Live DineFlow GPS Map View
            </Text>
            <Text style={{ color: "#5f6368", fontSize: 13, marginTop: 4 }}>
                Delivery location tracking active
            </Text>
            {children}
        </View>
    );
};

export default MapView;
