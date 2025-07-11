// src/components/BookList.jsx
import React, { useEffect, useState } from "react";
import BookForm from "./BookForm";

export default function BookList() {
  const [books, setBooks] = useState([]);
  const [selectedBook, setSelectedBook] = useState(null);

  const fetchBooks = async () => {
    try {
      const res = await fetch("http://localhost:8000/books");
      const data = await res.json();
      setBooks(data);
    } catch (err) {
      console.error("❌ Error cargando libros:", err.message);
    }
  };

  const eliminarLibro = async (id) => {
    try {
      await fetch(`http://localhost:8000/books/${id}`, {
        method: "DELETE",
      });
      setBooks(books.filter((b) => b.id !== id));
    } catch (err) {
      console.error("❌ Error al eliminar:", err.message);
    }
  };

  const handleBookAdded = (nuevoLibro) => {
    setBooks([...books, nuevoLibro]);
  };

  const handleBookUpdated = (libroActualizado) => {
    setBooks(
      books.map((book) =>
        book.id === libroActualizado.id ? libroActualizado : book
      )
    );
    setSelectedBook(null); // Limpiar formulario después de editar
  };

  useEffect(() => {
    fetchBooks();
  }, []);

  return (
    <>
      <h2>📚 Biblioteca Digital</h2>
      <BookForm
        onBookAdded={handleBookAdded}
        onBookUpdated={handleBookUpdated}
        selectedBook={selectedBook}
      />
      <h3>📚 Lista de Libros</h3>
      <ul>
        {books.map((book) => (
          <li key={book.id}>
            {book.title} — {book.author} ({book.genre}){" "}
            {book.available ? "✅ Disponible" : "❌ Prestado"}
            <button onClick={() => setSelectedBook(book)}>Editar</button>
            <button onClick={() => eliminarLibro(book.id)}>Eliminar</button>
          </li>
        ))}
      </ul>
    </>
  );
}
