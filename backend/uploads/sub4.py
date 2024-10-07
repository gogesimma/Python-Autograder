
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
def add_numbers(a, b):
    return a + b

#3
def greet(name):
    return f"Hello, {name}!"