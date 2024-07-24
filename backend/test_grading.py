# test_grading.py
import pytest
from grading import run_test_case

def test_run_test_case():
    test_case = {'input': '5\n', 'output': '25'}  # Updated expected output
    student_code_path = 'student_code.py'

    # Create a dummy student code file for testing
    with open(student_code_path, 'w') as f:
        f.write("""
input_data = input().strip()
output = int(input_data) ** 2
print(output)
        """)

    result, output = run_test_case(student_code_path, test_case)
    print(f"Result: {result}, Output: '{output}', Expected: '{test_case['output']}'")
    assert result == True, f"Expected True but got {result}. Output: {output}"

if __name__ == "__main__":
    test_run_test_case()
    print("All tests passed.")
