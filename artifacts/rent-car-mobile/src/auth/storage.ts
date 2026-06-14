import * as SecureStore from "expo-secure-store";
import AsyncStorage from "@react-native-async-storage/async-storage";
import { Platform } from "react-native";
import type { Profile, Role, Tokens } from "@/api/types";

const TOKEN_KEY = "src_tokens_v1";
const PROFILE_KEY = "src_profile_v1";

const isWeb = Platform.OS === "web";

async function setItem(key: string, value: string) {
  if (isWeb) return AsyncStorage.setItem(key, value);
  return SecureStore.setItemAsync(key, value);
}
async function getItem(key: string): Promise<string | null> {
  if (isWeb) return AsyncStorage.getItem(key);
  return SecureStore.getItemAsync(key);
}
async function delItem(key: string) {
  if (isWeb) return AsyncStorage.removeItem(key);
  return SecureStore.deleteItemAsync(key);
}

export async function saveTokens(tokens: Tokens) {
  await setItem(TOKEN_KEY, JSON.stringify(tokens));
}
export async function getTokens(): Promise<Tokens | null> {
  const raw = await getItem(TOKEN_KEY);
  if (!raw) return null;
  try { return JSON.parse(raw) as Tokens; } catch { return null; }
}

const authResetListeners = new Set<() => void>();
export function onAuthReset(cb: () => void): () => void {
  authResetListeners.add(cb);
  return () => authResetListeners.delete(cb);
}
function emitAuthReset() {
  authResetListeners.forEach((cb) => { try { cb(); } catch { } });
}

export async function clearTokens() {
  await delItem(TOKEN_KEY);
  await delItem(PROFILE_KEY);
  emitAuthReset();
}

export async function saveProfile(role: Role, profile: Profile) {
  await AsyncStorage.setItem(PROFILE_KEY, JSON.stringify({ role, profile }));
}
export async function getProfile(): Promise<{ role: Role; profile: Profile } | null> {
  const raw = await AsyncStorage.getItem(PROFILE_KEY);
  if (!raw) return null;
  try { return JSON.parse(raw); } catch { return null; }
}
