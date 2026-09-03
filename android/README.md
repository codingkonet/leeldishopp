# LebeldiShop Android app

This is a native Android WebView wrapper for the hosted PHP/MySQL store. It keeps the store pages,
login session, checkout, WhatsApp links, admin uploads, and digital downloads available inside an
APK while opening external links in the appropriate Android app.

## Configure the production URL

The default URL is `https://lebeldishop.com/`. To build for a different deployed domain:

```bash
./gradlew assembleRelease -PSTORE_URL=https://your-domain.example/
```

You can also edit `STORE_URL` in `app/src/main/java/com/lebeldishop/app/MainActivity.kt` only if
you prefer a fixed URL.

## Build with Android Studio

1. Install Android Studio and Android SDK 35.
2. Open the `android/` folder.
3. Let Gradle sync and install missing SDK components.
4. Select `app` and build `Build > Generate App Bundles or APKs > Generate APKs`.
5. The debug APK is created under `app/build/outputs/apk/debug/`.
6. For Play Store, generate a signed release APK/AAB from Android Studio.

## Build from a terminal

From this directory, with Java 17 and the Android SDK configured:

```bash
gradle assembleDebug -PSTORE_URL=https://your-domain.example/
```

The project currently has no Gradle wrapper. Android Studio can generate one, or install Gradle
locally. The PHP website must be deployed over HTTPS before publishing the APK.
