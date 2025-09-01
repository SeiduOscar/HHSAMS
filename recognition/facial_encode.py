import sys
import json
import face_recognition
import os

if len(sys.argv) < 2:
    print(json.dumps({"status": "error", "message": "No image path provided"}))
    sys.exit()

image_path = sys.argv[1]

if not os.path.exists(image_path):
    print(json.dumps({"status": "error", "message": "Image file not found"}))
    sys.exit()

try:
    image = face_recognition.load_image_file(image_path)
    encodings = face_recognition.face_encodings(image)

    if len(encodings) == 0:
        print(json.dumps({"status": "error", "message": "No face detected in the image"}))
    else:
        print(json.dumps({
            "status": "success",
            "face_encoding": encodings[0].tolist()
        }))

except Exception as e:
    print(json.dumps({"status": "error", "message": str(e)}))
