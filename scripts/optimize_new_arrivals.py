from pathlib import Path
from PIL import Image


SOURCE = Path(__file__).resolve().parents[1] / "wordpress" / "wp-content" / "uploads" / "2026" / "10" / "luxury-new-arrivals"


def main() -> None:
    converted = []
    for source in sorted(SOURCE.glob("*.png")):
        target = source.with_suffix(".webp")
        with Image.open(source) as image:
            image.convert("RGB").save(target, "WEBP", quality=88, method=6)
        converted.append((target.name, target.stat().st_size))

    total = sum(size for _, size in converted)
    print(f"converted={len(converted)} total_mb={total / 1024 / 1024:.2f}")


if __name__ == "__main__":
    main()
