---
name: Android Gradle caching
description: CI de Android nativo con Expo prebuild y compilación C++ de React Native.
---

El wrapper de Gradle de una app Expo gestionada aparece después de `expo prebuild`, así que el caché de Gradle debe configurarse después de ese paso; `setup-java` con `cache: gradle` falla si el wrapper aún no existe.

**Why:** GitHub Actions ejecuta `setup-java` durante el checkout, mientras que el proyecto `android/` se genera y se limpia más tarde; el intento de cachearlo demasiado pronto falla antes de compilar.

**How to apply:** Usa `gradle/actions/setup-gradle` después de `prebuild`. Para acelerar CMake/Reanimated en AAB, limita `reactNativeArchitectures` a las ABI realmente necesarias y cachea los directorios `.cxx`.