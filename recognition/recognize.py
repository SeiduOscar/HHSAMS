import warnings
warnings.simplefilter("ignore")
import sys, json, face_recognition, numpy as np

def face_confidence(face_distance, threshold=0.6):
    if face_distance > threshold:
        return 0.0
    range_val = (1.0 - face_distance) / (1.0 - threshold)
    return round(range_val * 100, 2)

if __name__ == "__main__":
    if len(sys.argv) < 3:
        print(json.dumps({"error": "Usage: python recognize.py <image_path> <encoding_json>"}))
        sys.exit(1)

    image_path = sys.argv[1]
    encoding_json = sys.argv[2]

    try:
        known_encoding = np.array(json.loads(encoding_json))
    except Exception as e:
        print(json.dumps({"error": f"Invalid encoding: {str(e)}"}))
        sys.exit(1)

    # Encode face from uploaded image
    image = face_recognition.load_image_file(image_path)
    encodings = face_recognition.face_encodings(image)

    if not encodings:
        print(json.dumps({"name": "No face found", "confidence": 0.0}))
        sys.exit(0)

    face_encoding = encodings[0]
    distance = face_recognition.face_distance([known_encoding], face_encoding)[0]
    confidence = face_confidence(distance)

    match = confidence > 0

    # ✅ Convert NumPy types to native Python before dumping
    print(json.dumps({
        "match": bool(match),
        "confidence": float(confidence)
    }))
