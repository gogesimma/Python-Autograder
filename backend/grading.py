import subprocess

def run_test_case(student_code_path, test_case):
    input_data = test_case['input']
    expected_output = test_case['output']

    try:
        result = subprocess.run(
            ['python', student_code_path],
            input=input_data,
            text=True,
            capture_output=True,
            timeout=5
        )
        output = result.stdout.strip()
        print(f"Subprocess returned with code {result.returncode}")
        if result.returncode != 0:
            print(f"Subprocess stderr: {result.stderr}")
        return output == expected_output, output
    except subprocess.TimeoutExpired:
        return False, 'Timeout'
    except Exception as e:
        return False, str(e)

