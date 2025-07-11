import  { useState, useEffect } from "react";
import BookList from "./components/BookList";
import BookForm from "./components/BookForm";

function App() {
  const [books, setBooks] = useState([]);

  const fetchBooks = async () => {
    try {
      const res = await fetch("http://localhost:8000/books");
      const data = await res.json();
      setBooks(data);
    } catch (err) {
      console.error("❌ Error cargando libros:", err.message);
    }
  };

  useEffect(() => {
    fetchBooks();
  }, []);

  const handleBookAdded = (nuevoLibro) => {
    setBooks([...books, nuevoLibro]); // Actualiza la lista visualmente
  };

  const handleBookDeleted = (id) => {
    setBooks(books.filter((b) => b.id !== id));
  };

  return (
    <div>
      <h1>📚 Biblioteca Digital</h1>
      <BookForm onBookAdded={handleBookAdded} />
      <BookList books={books} onDelete={handleBookDeleted} />
    </div>
  );
}

export default App;
