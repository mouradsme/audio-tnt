import sys
import json
import argostranslate.package
import argostranslate.translate

def install_languages():
    # Download and install language packages
    argostranslate.package.update_package_index()
    available_packages = argostranslate.package.get_available_packages()
    package_to_install = next(
        filter(
            lambda x: x.from_code == "en" and x.to_code == "fr", available_packages
        )
    )
    argostranslate.package.install_from_path(package_to_install.download())

def translate_text(text, target_lang):
    try:
        # Get installed languages
        installed_languages = argostranslate.translate.get_installed_languages()
        
        # Find source and target languages
        from_lang = None
        to_lang = None
        
        # Try to detect source language
        for lang in installed_languages:
            if lang.code == target_lang:
                to_lang = lang
            # Try to find a translation path
            translation = lang.get_translation(to_lang)
            if translation:
                from_lang = lang
                break
        
        if not from_lang or not to_lang:
            return {"error": "Language not supported"}
        
        # Translate the text
        translation = from_lang.get_translation(to_lang)
        translated_text = translation.translate(text)
        
        return {
            "translation": translated_text,
            "source_language": from_lang.code,
            "target_language": to_lang.code
        }
        
    except Exception as e:
        return {"error": str(e)}

if __name__ == "__main__":
    # Get input from command line arguments
    if len(sys.argv) != 3:
        print(json.dumps({"error": "Usage: python translate.py <text> <target_language>"}))
        sys.exit(1)
    
    text = sys.argv[1]
    target_lang = sys.argv[2]
    
    # Install languages if needed
    install_languages()
    
    # Translate the text
    result = translate_text(text, target_lang)
    print(json.dumps(result)) 