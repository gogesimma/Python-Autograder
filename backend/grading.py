import subprocess
import json 
import sys
import _mysql_connector



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
    
def grade_submission(student_code_path, submission_id):
    # Connect to the database
    conn = _mysql_connector.connect(
        host="localhost",
        user="root",  # Your MySQL username
        password="simakahle@10#",  # Your MySQL password
        database="autograder"
    )
    cursor = conn.cursor(dictionary=True)

    # Fetch test cases
    cursor.execute("SELECT * FROM test_cases")
    test_cases = cursor.fetchall()
    results = []
    for test_case in test_cases:
        result, output = run_test_case(student_code_path, test_case)
        results.append({
            'test_case_id': test_case['id'],
            'result': result,
            'output': output
        })
    
    for result in results:
        cursor.execute("""
            INSERT INTO results (submission_id, test_case_id, output, result)
            VALUES (%s, %s, %s, %s)
        """, (submission_id, result['test_case_id'], result['output'], 'pass' if result['result'] else 'fail'))
      
    conn.commit()
    cursor.close()
    conn.close()

if __name__ == "__main__":
    student_code_path = sys.argv[1]
    submission_id = sys.argv[2]
    grade_submission(student_code_path, submission_id)
    with open('test_cases. json') as f:
        test_cases = json.load(f)

   