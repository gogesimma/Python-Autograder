
import sys

def main():
    # Check if there is an input argument
    if len(sys.argv) > 1:
        input_data = sys.argv[1].strip()

        try:
            # Convert input to integer and calculate square
            number = int(input_data)
            result = number ** 2
            print(result)
        except ValueError:
            # Handle case where input is not a valid integer
            print(f"Invalid input: {input_data}")
    else:
        print("No input provided")

if __name__ == "__main__":
    main()