import { Link, Stack } from "expo-router";
import React from "react";
import { StyleSheet, Text, View } from "react-native";
import { colors } from "@/theme/colors";
import { useThemedStyles } from "@/theme/ThemeContext";

export default function NotFoundScreen() {
  const styles = useThemedStyles(makeStyles);
  return (
    <>
      <Stack.Screen options={{ title: "Oops!" }} />
      <View style={styles.container}>
        <Text style={styles.title}>Esta pantalla no existe.</Text>
        <Link href="/(client)/cars" style={styles.link}>
          <Text style={{ color: colors.primaryDark }}>Ver autos disponibles</Text>
        </Link>
      </View>
    </>
  );
}

const makeStyles = () => StyleSheet.create({
  container: { flex: 1, alignItems: "center", justifyContent: "center", padding: 20, backgroundColor: colors.bg },
  title: { fontSize: 20, fontWeight: "bold", color: colors.text },
  link: { marginTop: 15, paddingVertical: 15 },
});
