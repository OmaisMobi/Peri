import preset from "../../../../vendor/filament/filament/tailwind.config.preset";

export default {
    presets: [preset],
    content: [
        "./app/Filament/SuperAdmin**/*.php",
        "./resources/views/filament/super-admin**/*.blade.php",
        "./vendor/filament/**/*.blade.php",
        "./vendor/jaocero/radio-deck/resources/views/**/*.blade.php",
        "./vendor/diogogpinto/filament-auth-ui-enhancer/resources/**/*.blade.php",
        "./vendor/guava/filament-knowledge-base/src/**/*.php",
        "./vendor/guava/filament-knowledge-base/resources/**/*.blade.php",
        "./vendor/kanuni/filament-cards/resources/**/*.blade.php",
    ],
};
