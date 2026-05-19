import React from "react";
import { Image, StyleSheet, Text, useWindowDimensions, View } from "react-native";
import { SafeAreaView } from "react-native-safe-area-context";
import { useRouter } from "expo-router";
import { Button } from "@/components/Button";
import { colors } from "@/theme/colors";
import { t } from "@/i18n";

export default function Welcome() {
  const router = useRouter();
  const { width } = useWindowDimensions();
  const logoSize = Math.min(160, Math.max(110, width * 0.32));

  return (
    <SafeAreaView style={styles.safe}>
      <View style={styles.container}>
        <View style={styles.hero}>
          <Image
            source={require("../assets/icon.png")}
            style={[styles.logo, { width: logoSize, height: logoSize }]}
            resizeMode="contain"
          />
          <Text style={styles.title}>{t("welcome.title")}</Text>
          <Text style={styles.sub}>{t("welcome.subtitle")}</Text>
        </View>
        <View style={styles.actions}>
          <Button title={t("welcome.asClient")} onPress={() => router.push("/login/client")} />
          <View style={{ height: 12 }} />
          <Button
            title={t("welcome.asStaff")}
            variant="secondary"
            onPress={() => router.push("/login/staff")}
          />
        </View>
      </View>
    </SafeAreaView>
  );
}

const styles = StyleSheet.create({
  safe: { flex: 1, backgroundColor: "#FFFFFF" },
  container: { flex: 1, width: "100%", maxWidth: 520, alignSelf: "center" },
  hero: { flex: 1, alignItems: "center", justifyContent: "center", padding: 24 },
  logo: { borderRadius: 28, marginBottom: 24 },
  title: { color: colors.text, fontSize: 28, fontWeight: "700", textAlign: "center" },
  sub: { color: colors.textMuted, fontSize: 15, marginTop: 8, textAlign: "center" },
  actions: { padding: 24, backgroundColor: "#FFFFFF" },
});
