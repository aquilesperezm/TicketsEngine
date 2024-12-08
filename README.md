# Ticket

## Description
It is a library embedded in a small work environment with Codeigniter 2.2.6 and PHP 5.6, which generates a ticket for cafes in PDF format. The libraries used are FPDF and QRCODE

### FPDF
FPDF is a PHP library that allows you to generate PDF documents in a simple and efficient way. Here is a summary of its main features:

#### FPDF Features
**Easy to use:** It provides a simple interface to create PDF documents without the need for advanced programming knowledge.

**Text support:** It allows you to add text with different fonts, sizes and styles (bold, italics, etc.).

**Images:** You can insert images in various formats (JPEG, PNG, GIF).

**Formatting:** It offers options to format the content, such as alignment, margins and colors.

**Pages:** You can create multiple pages and control their layout.
No dependencies: FPDF is independent and does not require other external libraries.

#### Scripts included into FPDF
1. Rotations
2. Forced justification
3. Dashed rectangle
4. Tags for cells and bold
5. Text rotations
6. 	Dashes
7. Code 128 barcodes
8. Line graph

### QRCODE
QR code library written in PHP5, distributed under LGPL license.
Strongly inspired by "QRcode Image PHP Scripts Version 0.50g (C)2000-2005,Y.Swetake"
(QR code is a registered trademark of DENSO WAVE INCORPORATED | http://www.denso-wave.com/qrcode/)

Allows QR code generation in PHP5.

---



## Importing the library in CodeIgniter 2.2.6
`$this->load->library('ticket/ticket');`

---

## Ticket's Methods

### `calcularSuma(a: number, b: number): number`

Calcula la suma de dos números.

#### Parámetros
- `a` (number): El primer número a sumar.
- `b` (number): El segundo número a sumar.

#### Valor de Retorno
- (number): La suma de `a` y `b`.

#### Ejemplo de Uso
```javascript
const resultado = calcularSuma(5, 10);
console.log(resultado); // Salida: 15




