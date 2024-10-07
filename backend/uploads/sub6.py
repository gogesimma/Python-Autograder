#1
import sys
def main():
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
if __name__ == "__main__":
    main()

#2
import sys
def add_numbers():
    if len(sys.argv) > 2:
        a = int(sys.argv[1])
        b = int(sys.argv[2])
        result = a + b
        print(result)
    else:
        print("Invalid input")
if __name__ == "__main__":
    add_numbers()

#3
import sys
def greet():
    if len(sys.argv) > 1:
        name = sys.argv[1].strip()
        print(f"Hello,{name}!")
    else:
        print("No input provided")
if __name__ == "__main__":
    greet()
