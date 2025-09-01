import face_recognition
import sys

if len(sys.argv) > 1:
    image_path = sys.argv[1].strip()
    try:
        image = face_recognition.load_image_file(image_path)
        face_locations = face_recognition.face_locations(image)

        if face_locations:
            print('face detected')
        else:
            print('no face detected')
    except Exception as e:
        print(f'Error: {e}')
else:
    print('no file was found')

