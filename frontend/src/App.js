import React from 'react';
import UploadForm from './components/UploadForm';

function App() {
  return (
    <div className="App">
      <header>
        <h1>Python Auto-Grader</h1>
        <nav>
          <a href="/">Home</a>
          <a href="/upload">Upload</a>
          <a href="/about">About</a>
          <a href="/contact">Contact</a>
        </nav>
      </header>
      <main>
        <UploadForm />
      </main>
      <footer>
        <p>&copy; 2024 Python Auto-Grader</p>
      </footer>
    </div>
  );
}

export default App;


