import React, { useState } from 'react';
import axios from 'axios';

function UploadForm() {
  const [file, setFile] = useState(null);
  const [message, setMessage] = useState('');

  const handleFileChange = (event) => {
    setFile(event.target.files[0]);
  };

  const handleSubmit = async (event) => {
    event.preventDefault();
    if (!file) {
      setMessage('Please select a file.');
      return;
    }

    const formData = new FormData();
    formData.append('file', file);

    try {
      const response = await axios.post('http://localhost:8000/upload.php', formData);
      setMessage(response.data.message);
    } catch (error) {
      setMessage('An error occurred while uploading the file.');
    }
  };

  return (
    <div>
      <h2>Upload Your Assignment</h2>
      <form onSubmit={handleSubmit}>
        <input type="file" onChange={handleFileChange} />
        <button type="submit">Upload</button>
      </form>
      {message && <p>{message}</p>}
    </div>
  );
}

export default UploadForm;





  