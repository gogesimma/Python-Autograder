#1
import sys

def square_number():
    if len(sys.argv) > 1:
        input_data = sys.argv[1].strip()
        try:
            number = int(input_data)
            result = number ** 2
            print(result)
        except ValueError:
            print(f"Invalid input: {input_data}")
    else:
        print("No input provided")

#2
import sys
def add_numbers():
    if len(sys.argv) > 2:
        try:
            a = int(sys.argv[1])
            b = int(sys.argv[2])
            result = a + b
            print(result)
        except ValueError:
            print(f"Invalid input: {sys.argv[1]},{sys.argv[2]}")
    else:
        print("Invalid input")

#3
import sys
def greet():
    if len(sys.argv) > 1:
        name = sys.argv[1].strip()
        print(f"Hello,{name}!")
    else:
        print("No input provided")

if __name__ == "__main__":
    if "#1" in sys.argv:
        square_number()
    elif "#2" in sys.argv:
        add_numbers()
    elif "#3" in sys.argv:
        greet()

