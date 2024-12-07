<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

class TicketGenerator extends CI_Controller
{

    private $FPDF;
    private $pound_sterling;

    private $cursor_x;
    private $cursor_y;

    private $products;

    private $sales;

    public function __construct()
    {
        parent::__construct();

        $params = array('P', 'in');
        $this->load->library('fpdf', $params);

        $this->FPDF = $this->fpdf;
        $this->pound_sterling = substr('£', 1, 1);


    }

    private function addNewPage($cursor_x = 35, $cursor_y = 5)
    {

        $this->FPDF->AddPage('P', array(190, 450));
        $this->cursor_y = $cursor_y;
        $this->cursor_x = $cursor_x;

    }

    public function generatepdf()
    {

        $this->load->library('ticket');
        $pdf = $this->ticket;



        $pdf->add_product([
            'nombre' => 'Ham & Cheese Baguette',
            'cantidad' => 1,
            'precio' => $pdf->float_rand(1.0, 10.0, 2),
            'hora' => '2pm',
            'graph_message' => 'Which often sells out by ',
            'rda' => 57,
            'calorias' => rand(100, 200),
            'graph_color' => ['Sales 1' => [rand(1, 255), rand(1, 255), rand(1, 255)]],
            'sales' => [
                'Sales 1' => [
                    '01-05' => rand(1, 30),
                    '05-10' => rand(1, 30),
                    '10-15' => rand(1, 30),
                    '15-20' => rand(1, 30),
                    '20-25' => rand(1, 30),
                    '25-30' => rand(1, 30)
                ]
            ]
        ]);

        $pdf->add_product([
            'nombre' => 'Chicken Caesar Salad',
            'cantidad' => 1,
            'precio' => $pdf->float_rand(1.0, 10.0, 2),
            'hora' => '1pm',
            'graph_message' => 'Which often sells out by ',
            'rda' => 57,
            'calorias' => rand(100, 200),
            'graph_color' => ['Sales 1' => [rand(1, 255), rand(1, 255), rand(1, 255)]],
            'sales' => [
                'Sales 1' => [
                    '01-05' => rand(1, 30),
                    '05-10' => rand(1, 30),
                    '10-15' => rand(1, 30),
                    '15-20' => rand(1, 30),
                    '20-25' => rand(1, 30),
                    '25-30' => rand(1, 30)
                ]
            ]
        ]);

        $params = array(
            'fecha' => '05-12-2024',
            'hora' => '7:18AM',
            'numero_referencia' => '842129-511',
            'numero_tickets' => '842-511',
            'url' => 'https://www.lecaroz.com'
        );


        $pdf->set_url_logo('assets/img/logo2.png');
        $pdf->set_details('<b>MONDAY</b> 15/04/2011,      <b>MASTERCARD *3452, EXP 04/13,   </b> <b>12.15:00PM</b> OR ' . utf8_decode('<b>LUCHTIME, £5.14</b>'), 14, 'L');
        $pdf->set_title(' <b>BREAD</b>    <b1>&</b1>     <b>BUTTER</b> ', 30, 'C');
        $pdf->set_address('32 GREAT EASTERN STREET, LONDON, EC2A 4RQ BREADBUTTER.COM | 020 8888 8888 | VAT 333 3333 33', 11, 'C');
        $pdf->set_order_number('2049');
        $pdf->set_iva('1.03');
        $pdf->set_factura($params);
        $pdf->set_price_phrase('IN <b>514</b>AD, VITALIUS LEADS A REBELLION IN THE BIZANTINE EMPIRE.');
        $pdf->set_rda_percent(rand(50, 100) . '%');
        $pdf->set_news('The <b>Nice Gallery</b> on <b>Great Eastern Street</b> is holding its opening night from 6pm. <b>(bit.ly/6h23b)</b>', 13);

        $pdf->render_ticket_pdf();


    }

    public function generateTicket()
    {
        $this->FPDF->SetMargins(35, 0, 35);
        //$this->FPDF->AddPage('P', array(190, 450));

        $this->FPDF->SetFont('Helvetica', 'B', 12);

        /**
         * rda - Recommended Dietary Allowance
         */

        function generateRandomValue($min, $max, array $exclude = array())
        {
            $range = array_diff(range($min, $max), $exclude);
            // array_shuffle($range);

            return array_shift($range);
        }

        $min = 1;
        $max = 30;

        $this->sales = array(
            array(
                'Sales 1' => array(
                    '01-05' => rand($min, $max),
                    '05-10' => rand($min, $max),
                    '10-15' => rand($min, $max),
                    '15-20' => rand($min, $max),
                    '20-25' => rand($min, $max),
                    '25-30' => rand($min, $max)
                )
            ),
            array(
                'Sales 2' => array(
                    '01-05' => rand($min, $max),
                    '05-10' => rand($min, $max),
                    '10-15' => rand($min, $max),
                    '15-20' => rand($min, $max),
                    '20-25' => rand($min, $max),
                    '25-30' => rand($min, $max)
                )
            ),
            array(
                'Sales 3' => array(
                    '01-05' => rand($min, $max),
                    '05-10' => rand($min, $max),
                    '10-15' => rand($min, $max),
                    '15-20' => rand($min, $max),
                    '20-25' => rand($min, $max),
                    '25-30' => rand($min, $max)
                )
            ),
            array(
                'Sales 4' => array(
                    '01-05' => rand($min, $max),
                    '05-10' => rand($min, $max),
                    '10-15' => rand($min, $max),
                    '15-20' => rand($min, $max),
                    '20-25' => rand($min, $max),
                    '25-30' => rand($min, $max)
                )
            ),
            array(
                'Sales 5' => array(
                    '01-05' => rand($min, $max),
                    '05-10' => rand($min, $max),
                    '10-15' => rand($min, $max),
                    '15-20' => rand($min, $max),
                    '20-25' => rand($min, $max),
                    '25-30' => rand($min, $max)
                )
            ),
            array(
                'Sales 6' => array(
                    '01-05' => rand($min, $max),
                    '05-10' => rand($min, $max),
                    '10-15' => rand($min, $max),
                    '15-20' => rand($min, $max),
                    '20-25' => rand($min, $max),
                    '25-30' => rand($min, $max)
                )
            ));

        $this->products = array(

            ['nombre' => 'Ham & Cheese Baguette', 'cantidad' => 1, 'precio' => 2.79, 'hora' => '2pm', 'graph_message' => 'Which often sells out by ', 'rda' => 57, 'calorias' => 120],
            ['nombre' => 'Chicken Caesar Salad', 'cantidad' => 1, 'precio' => 2.79, 'hora' => '2pm', 'graph_message' => 'Which often sells out by ', 'rda' => 38, 'calorias' => 206],
            ['nombre' => 'Grilled Cheese Sandwich', 'cantidad' => 1, 'precio' => 2.79, 'hora' => '2pm', 'graph_message' => 'Which often sells out by ', 'rda' => 25, 'calorias' => 134],
            ['nombre' => 'Vegetable Soup', 'cantidad' => 1, 'precio' => 2.79, 'hora' => '2pm', 'graph_message' => 'Which often sells out by ', 'rda' => 73, 'calorias' => 78],
            ['nombre' => 'Spaghetti Bolognese', 'cantidad' => 1, 'precio' => 2.79, 'hora' => '2pm', 'graph_message' => 'Which often sells out by ', 'rda' => 14, 'calorias' => 92],
            ['nombre' => 'Chicken Stir Fry', 'cantidad' => 1, 'precio' => 2.79, 'hora' => '2pm', 'graph_message' => 'Which often sells out by ', 'rda' => 46, 'calorias' => 62],

        );

        $this->FPDF->AddPage('P', array(190, 450));
        // $this->FPDF->AddPage('P','A4');

        $this->products = array_slice($this->products, 0, 2);


        $this->section_1();
        $this->section_2();
        $this->set_products($this->products);
        $this->section_3();
        $this->section_4();


        $this->FPDF->Output('I', 'report.pdf');

    }

    private function section_1()
    {

        $this->new_blank_line(5, 'B', 0.5);
        $this->new_blank_line(5, '', 0.5);

        $this->FPDF->SetDrawColor(169, 169, 169);
        $this->FPDF->SetDash(1, 1);
        $this->FPDF->Cell(20, 5, '', 'TR', 0);
        $this->FPDF->SetDash();
        $this->FPDF->SetDrawColor(0, 0, 0);
        $this->FPDF->Cell(5, 5, '', 0, 0);
        $this->FPDF->Cell(90, 5, '', 'LTR', 0);
        $this->FPDF->Cell(0, 5, '', '', 1);

        for ($i = 0; $i < 4; $i++) {

            $this->FPDF->SetDash(1, 1);
            $this->FPDF->SetDrawColor(169, 169, 169);
            $this->FPDF->Cell(20, 5, '', 'LR', 0);
            $this->FPDF->SetDash();
            $this->FPDF->SetDrawColor(0, 0, 0);
            $this->FPDF->Cell(5, 5, '', 0, 0);
            $this->FPDF->Cell(90, 5, '', 'LR', 0);
            $this->FPDF->Cell(0, 5, '', '', 1);
        }

        $this->FPDF->SetDash(1, 1);
        $this->FPDF->SetDrawColor(169, 169, 169);
        $this->FPDF->Cell(20, 5, '', 'LBR', 0);
        $this->FPDF->SetDash();
        $this->FPDF->SetDrawColor(0, 0, 0);
        $this->FPDF->Cell(5, 5, '', 0, 0);
        $this->FPDF->Cell(90, 5, '', 'LR', 0);
        $this->FPDF->Cell(0, 5, '', '', 1);

        $this->FPDF->Cell(20, 5, '', '', 0);
        $this->FPDF->Cell(5, 5, '', 0, 0);
        $this->FPDF->Cell(90, 5, '', 'LR', 0);
        $this->FPDF->Cell(0, 5, '', '', 1);

        $this->FPDF->Cell(20, 5, '', '', 0);
        $this->FPDF->Cell(5, 5, '', 0, 0);
        $this->FPDF->Cell(90, 5, '', 'LR', 0);
        $this->FPDF->Cell(0, 5, '', '', 1);

        $this->FPDF->Cell(20, 5, '', '', 0);
        $this->FPDF->Cell(5, 5, '', 0, 0);
        $this->FPDF->Cell(90, 5, '', 'LR', 0);
        $this->FPDF->Cell(0, 5, '', '', 1);

        $this->FPDF->Cell(20, 5, '', '', 0);
        $this->FPDF->Cell(5, 5, '', 0, 0);
        $this->FPDF->Cell(90, 5, '', 'LR', 0);
        $this->FPDF->Cell(0, 5, '', '', 1);

        $this->FPDF->Cell(20, 5, '', '', 0);
        $this->FPDF->Cell(5, 5, '', 0, 0);
        $this->FPDF->Cell(90, 5, '', 'LBR', 0);
        $this->FPDF->Cell(0, 5, '', '', 1);

        $this->FPDF->SetDash();
        $this->FPDF->SetDrawColor(0, 0, 0);
        $this->FPDF->Line(35, 40, 35, 10);

        $this->FPDF->Cell(0, 5, '', '', 1);

        $this->FPDF->SetDash(1, 1);
        $this->FPDF->SetDrawColor(169, 169, 169);
        $this->FPDF->Line(35, 70, 155, 70);
        $this->FPDF->SetDash();
        $this->FPDF->SetDrawColor(0, 0, 0);

        $this->FPDF->SetTextColor(169, 169, 169);
        $this->FPDF->SetFont('Helvetica', 'B', 9);
        $this->FPDF->TextWithRotation(45, 35, 'STAPLE/CUP', 90);

        $this->FPDF->SetFont('Helvetica', 'B', 7);
        $this->FPDF->Text(63, 15, 'DETALLES');
        $this->FPDF->SetDrawColor(169, 169, 169);
        $this->FPDF->Line(62, 17, 145, 17);

        $this->FPDF->SetTextColor(0, 0, 0);
        $this->FPDF->SetFont('Helvetica', 'B', 14);

        /* $this->FPDF->Text(63, 25, 'MONDAY');
         $this->FPDF->SetFont('Helvetica', '', 14);
         $this->FPDF->Text(89, 25, 'THE');
         $this->FPDF->SetFont('Helvetica', 'B', 14);
         $this->FPDF->Text(102, 25, '15/04/2011');

         $this->FPDF->SetFont('Helvetica', 'B', 14);
         $this->FPDF->Text(63, 30, 'MASTERCARD *3452, EXP 04/13,');

         $this->FPDF->SetFont('Helvetica', 'B', 14);
         $this->FPDF->Text(63, 35, '12.15PM,');
         $this->FPDF->SetFont('Helvetica', '', 14);
         $this->FPDF->Text(85, 35, 'OR');
         $this->FPDF->SetFont('Helvetica', 'B', 14);

         $this->FPDF->Text(93, 35, 'LUNCHTIME, ' . $this->pound_sterling . '5.14');*/

        $this->FPDF->SetXY(64, 20);
        $this->FPDF->WriteText('<LUNES> 15/04/2011,');

        $this->FPDF->SetXY(64, 26);
        $this->FPDF->WriteText('<MASTERCARD *3452, EXP 04/03,>');

        $this->FPDF->SetXY(64, 32);
        $this->FPDF->WriteText('<12. 15:00> O <' . utf8_decode('ALMUERZO: £ 5,14') . '>');


        $this->FPDF->SetXY(64, 40);
        $this->FPDF->SetDrawColor(169, 169, 169);
        $this->FPDF->SetTextColor(169, 169, 169);
        $this->FPDF->SetFont('Helvetica', 'B', 7);
        $this->FPDF->Cell(82, 5, 'NOTAS', 'LTR', 1);
        $this->FPDF->SetXY(64, 40);
        $this->FPDF->Cell(82, 20, '', 1);

        $this->FPDF->SetXY(35, 70);

        $this->cursor_x = 35;
        $this->cursor_y = 70;


    }

    private function section_2()
    {

        $this->FPDF->SetDrawColor(0, 0, 0);
        $this->FPDF->Cell(120, 5, '', '', 1, 1);

        $this->FPDF->SetLineWidth(2);
        $this->FPDF->Line(40, 75, 150, 75);

        //border

        /*$border_length = 12 + count($this->productos) * 12;

        $this->FPDF->SetLineWidth(0.5);
        for ($i = 0; $i < $border_length; $i++) {
            $this->FPDF->Cell(120, 10, '', 'LR', 1, 1);
        }*/

        $this->FPDF->SetTextColor(0, 0, 0);
        $sample_text = '';

//Test1
        $test1 = array();
        $test1['bullet'] = chr(149);
        $test1['margin'] = '';
        $test1['indent'] = 0;
        $test1['spacer'] = 0;
        $test1['text'] = array();

        $test2['bullet'] = chr(149);
        $test2['margin'] = '';
        $test2['indent'] = 0;
        $test2['spacer'] = 0;
        $test2['text'] = array();

        $test1['text'][0] = $sample_text;
        $test2['text'][0] = '';

        $this->FPDF->SetXY(45, 63);
        $this->FPDF->SetFont('Helvetica', 'B', 30);
        $this->FPDF->MultiCellBltArray(60, 46, $test1);

        // Title of the ticket
        /*$this->FPDF->SetFont('Helvetica', '', 20);
        $this->FPDF->Text(90, 88, '&');
        $this->FPDF->SetFont('Helvetica', 'B', 30);
        $this->FPDF->Text(96, 89, 'BUTTER');
*/
        $this->FPDF->SetXY(50, 83);
        $this->FPDF->WriteText('<BREAD> & <BUTTER>');

        $this->FPDF->SetFont('Helvetica', 'B', 30);
        $this->FPDF->SetXY(145, 81);
        $this->FPDF->MultiCellBltArray(10, 10, $test2);

        $this->FPDF->SetXY(140, 90);
        $this->FPDF->SetLineWidth(1);
        $this->FPDF->Line(45, 95, 143, 95);

        $this->FPDF->SetFont('Helvetica', '', 10);
        //$this->FPDF->SetXY(140,100);
        $this->FPDF->SetXY(53, 99);
        $this->FPDF->WriteText('32 GREAT EASTERN STREET, LONDON, EC2A 4RQ');

        $this->FPDF->SetXY(50, 104);
        $this->FPDF->WriteText('BREADBUTTER.COM | 020 8888 8888 | VAT 333 3333 33');

        //$this->FPDF->Text(53, 103, '32 GREAT EASTERN STREET, LONDON, EC2A 4RQ');
        //$this->FPDF->Text(50, 108, 'BREADBUTTER.COM | 020 8888 8888 | VAT 333 3333 33');

        $this->FPDF->SetLineWidth(2);
        $this->FPDF->Line(40, 114, 150, 114);
        $this->FPDF->SetFont('Helvetica', '', 8);
        $this->FPDF->Text(40, 119, 'NO. ORDEN: ');
        $this->FPDF->SetFont('Helvetica', 'B', 8);
        $this->FPDF->Text(57, 119, '2049');

        $this->FPDF->Image('assets/img/banner11.png', 65, 117);

        $this->FPDF->SetFont('Helvetica', 'B', 40);
        $this->FPDF->Text(80, 140, $this->pound_sterling . '5.14');
        $this->FPDF->SetFont('Helvetica', '', 10);
        $this->FPDF->Text(73, 153, 'INCLUYE IVA DE ');
        $this->FPDF->SetFont('Helvetica', 'BU', 10);
        $this->FPDF->Text(103, 153, $this->pound_sterling . '1.03');

        $this->FPDF->Image('assets/img/banner22.png', 37, 147);

        $this->FPDF->SetFont('Helvetica', 'B', 12);
        $this->FPDF->TextWithRotation(43, 158, 'PRICE', 15);
        $this->FPDF->TextWithRotation(45, 165, 'FACT!', 15);


        /*$this->FPDF->SetFont('Helvetica', '', 11);
        $this->FPDF->SetXY(65, 165);
        $this->FPDF->Cell(180, 5, 'IN ', '0');
        $this->FPDF->SetFont('Helvetica', 'B', 11);
        $this->FPDF->SetXY(70, 165);
        $this->FPDF->Cell(190, 5, '514', '0');
        $this->FPDF->SetFont('Helvetica', '', 11);
        $this->FPDF->SetXY(77, 165);
        $this->FPDF->Cell(203, 5, 'AD, VITALIUS LEADS A ', '0');
        $this->FPDF->SetXY(58, 170);
        $this->FPDF->Cell(170, 5, 'REBELLION IN THE BIZANTINE EMPIRE.', '0');*/

        $this->FPDF->SetFont('Helvetica', '', 12);
        $this->FPDF->SetXY(67, 165);
        $this->FPDF->WriteText('IN <514>AD, VITALIUS LEADS A');
        $this->FPDF->SetXY(56, 172);
        $this->FPDF->WriteText('REBELLION IN THE BIZANTINE EMPIRE.');


        //$this->FPDF->SetXY(60,180);
        $this->FPDF->SetFont('Helvetica', 'BU', 11);
        $this->FPDF->Text(80, 190, 'Y COMPRASTE:', 1);

        //here dynamic product fields

    }

    private function set_products($productos = null)
    {

        $this->products = $productos;

        $this->FPDF->SetLineWidth(0.5);
        $this->FPDF->SetXY(40, 195);
        $this->FPDF->SetFont('Helvetica', '', 12);

        // $block_y = 195;
        $this->cursor_y = 195;
        $block_x = 40;

        $min = 0;
        $max = 255;

        $colors = array(
            'Sales 1' => array(rand($min, $max), rand($min, $max), rand($min, $max)),
            'Sales 2' => array(rand($min, $max), rand($min, $max), rand($min, $max)),
            'Sales 3' => array(rand($min, $max), rand($min, $max), rand($min, $max)),
            'Sales 4' => array(rand($min, $max), rand($min, $max), rand($min, $max)),
            'Sales 5' => array(rand($min, $max), rand($min, $max), rand($min, $max)),
            'Sales 6' => array(rand($min, $max), rand($min, $max), rand($min, $max)),
        );


        for ($i = 0; $i < count($this->products); $i++) {


            if ($i % 2 == 0) {

                if ($i == count($this->products) - 1) {

                    $block_x = 70;
                    $this->FPDF->SetXY($block_x, $this->cursor_y);
                    $this->FPDF->SetFont('Helvetica', '', 12);
                    $this->FPDF->WriteText('<' . $productos[$i]['cantidad'] . '> @ ' . $this->pound_sterling . $productos[$i]['precio']);

                    $this->FPDF->SetXY($block_x - 10, $this->cursor_y + 5);
                    $this->FPDF->LineGraph(70, 20, $this->sales[$i], 'HV', $colors, 10, 7, FALSE, FALSE, FALSE);

                    // $this->FPDF->Image('assets/img/product_graph_1.png', $block_x, $this->cursor_y + 5);
                    $this->FPDF->SetFont('Helvetica', '', 12);
                    $this->FPDF->SetXY($block_x, $this->cursor_y + 20);
                    $this->FPDF->WriteText('<' . $productos[$i]['nombre'] . '>');
                    $this->FPDF->SetXY($block_x, $this->cursor_y + 25);
                    $this->FPDF->SetFont('Helvetica', '', 10);
                    $this->FPDF->WriteText($productos[$i]['graph_message'] . $productos[$i]['hora']);

                } else {

                    $block_x = 40;
                    $this->FPDF->SetXY($block_x, $this->cursor_y);
                    $this->FPDF->SetFont('Helvetica', '', 12);
                    $this->FPDF->WriteText('<' . $productos[$i]['cantidad'] . '> @ ' . $this->pound_sterling . $productos[$i]['precio']);

                    $this->FPDF->SetXY($block_x - 10, $this->cursor_y + 5);
                    $this->FPDF->LineGraph(70, 20, $this->sales[$i], 'HV', $colors, 10, 7, FALSE, FALSE, FALSE);

                    // $this->FPDF->Image('assets/img/product_graph_1.png', $block_x, $this->cursor_y + 5);
                    $this->FPDF->SetFont('Helvetica', '', 12);
                    $this->FPDF->SetXY($block_x, $this->cursor_y + 20);
                    $this->FPDF->WriteText('<' . $productos[$i]['nombre'] . '>');
                    $this->FPDF->SetXY($block_x, $this->cursor_y + 25);
                    $this->FPDF->SetFont('Helvetica', '', 10);
                    $this->FPDF->WriteText($productos[$i]['graph_message'] . $productos[$i]['hora']);
                }


            } else {

                $block_x += 60;
                $this->FPDF->SetXY($block_x, $this->cursor_y);
                $this->FPDF->SetFont('Helvetica', '', 12);
                $this->FPDF->WriteText('<' . $productos[$i]['cantidad'] . '> @ ' . $this->pound_sterling . $productos[$i]['precio']);

                $this->FPDF->SetXY($block_x - 10, $this->cursor_y + 5);
                $this->FPDF->LineGraph(70, 20, $this->sales[$i], 'HV', $colors, 10, 7, FALSE, FALSE, FALSE);

                //   $this->FPDF->Image('assets/img/product_graph_1.png', $block_x, $this->cursor_y + 5);
                $this->FPDF->SetFont('Helvetica', '', 12);
                $this->FPDF->SetXY($block_x, $this->cursor_y + 20);
                $this->FPDF->WriteText('<' . $productos[$i]['nombre'] . '>');
                $this->FPDF->SetXY($block_x, $this->cursor_y + 25);
                $this->FPDF->SetFont('Helvetica', '', 10);
                $this->FPDF->WriteText($productos[$i]['graph_message'] . $productos[$i]['hora']);

                $this->cursor_y += 35;

            }

        }

        $this->cursor_x = 36;


    }


    private function section_3()
    {

        $this->closeBorderLines();

        if (count($this->products) >= 9 && count($this->products) <= 12) {
            $this->addNewPage(35, 5);
        }

        if (count($this->products) % 2 != 0 && (count($this->products) < 9 || count($this->products) > 12))
            $this->cursor_y += 35;

        $this->FPDF->Image('assets/img/banner4.png', $this->cursor_x, $this->cursor_y);

        $this->FPDF->SetXY(46, $this->cursor_y += 13);
        $this->FPDF->SetFont('Helvetica', '', 9);
        $this->FPDF->Cell(50, 5, 'EN TOTAL, ESTO SUMA : ', 0, 1);

        $this->FPDF->SetXY(113, $this->cursor_y += 2);
        $this->FPDF->SetFont('Helvetica', '', 9);
        $this->FPDF->Cell(50, 5, 'O APROXIM.: ', 0, 0);

        $this->FPDF->SetFont('Helvetica', 'B', 40);
        $this->FPDF->SetXY(53, $this->cursor_y += 8);
        $this->FPDF->Cell(50, 5, '986', 0, 1);

        $this->FPDF->SetXY(111, $this->cursor_y + 5);
        $this->FPDF->SetFont('Helvetica', 'B', 40);
        $this->FPDF->Cell(50, 5, '45%', 0, 1);

        $this->FPDF->SetFont('Helvetica', '', 9);
        $this->FPDF->SetXY(57, $this->cursor_y += 11);
        $this->FPDF->Cell(50, 5, 'CALORIAS,', 0, 1);

        $this->FPDF->SetXY(111, $this->cursor_y + 3);
        $this->FPDF->SetFont('Helvetica', '', 9);
        $this->FPDF->Cell(50, 5, 'DE TU RDA.', 0, 1);

        $this->FPDF->Cell(20, 5, '', 0, 0);

        $this->FPDF->SetFont('Helvetica', 'B', 12);
        $this->FPDF->TextWithRotation(75, $this->cursor_y += 27, utf8_decode('¿AÚN MÁS TARDE?'), 2);
        $this->FPDF->SetFont('Helvetica', '', 11);

        $this->FPDF->SetXY(44, $this->cursor_y + 3);
        $this->FPDF->WriteTextWithRotation(43, $this->cursor_y + 20, 'The <Nice Gallery> on <Great Eastern Street> is holding its', 2);

        $this->FPDF->SetXY(58, $this->cursor_y + 8);
        $this->FPDF->WriteTextWithRotation(43, $this->cursor_y + 20, 'opening night from 6pm. <(bit.ly/6h23b)>', 2);

        /* $this->FPDF->SetFont('Helvetica', '', 12);
         $this->FPDF->TextWithRotation(43, $this->cursor_y + 6, 'The', 2);
         $this->FPDF->SetFont('Helvetica', 'B', 12);
         $this->FPDF->TextWithRotation(51, $this->cursor_y + 6, 'Nice Gallery', 2);
         $this->FPDF->SetFont('Helvetica', '', 12);
         $this->FPDF->TextWithRotation(76, $this->cursor_y + 5 + 0.2, 'on', 2);
         $this->FPDF->SetFont('Helvetica', 'B', 12);
         $this->FPDF->TextWithRotation(81, $this->cursor_y + 5 + 0.2, 'Great Eastern Street', 2);
         $this->FPDF->SetFont('Helvetica', '', 12);
         $this->FPDF->TextWithRotation(123, $this->cursor_y + 4, 'is holding its', 2);*/

        // $this->FPDF->SetXY(40, 255);
        //  $this->FPDF->TextWithRotation(58, $this->cursor_y + 11, 'opening night from 6pm.', 2);
        //  $this->FPDF->SetFont('Helvetica', 'B', 12);
        //  $this->FPDF->TextWithRotation(105, $this->cursor_y + 10, '(bit.ly/6h23b)', 2);


        $this->FPDF->SetDash(1, 1);
        $this->FPDF->SetDrawColor(169, 169, 169);
        $this->FPDF->Line(35, $this->cursor_y + 24, 155, $this->cursor_y + 24);
        $this->FPDF->SetDash();

        $this->FPDF->Image('assets/img/banner5.png', 45, $this->cursor_y + 20);

        $this->cursor_y += 25;

    }

    private function section_4()
    {

        if (count($this->products) >= 5 && count($this->products) <= 8)
            $this->addNewPage(35, 0);


        //border
        $this->FPDF->SetLineWidth(0.5);
        $this->FPDF->SetDrawColor(0, 0, 0);


        $this->FPDF->Image('assets/img/banner6.png', 35, $this->cursor_y + 3);

        $this->FPDF->SetXY(40, $this->cursor_y + 9);
        $this->FPDF->Cell(30, 5, '', '', 0, 0);
        $this->FPDF->SetFont('Helvetica', 'B', 10);
        $this->FPDF->Cell(40, 4, utf8_decode('¿ERES UN CLIENTE REGULAR?'), '', 1, 1);
        $this->FPDF->SetXY(30, $this->cursor_y + 14);
        $this->FPDF->Cell(15, 5, '', '', 0, 0);
        $this->FPDF->SetFont('Helvetica', '', 10);
        $this->FPDF->Cell(45, 5, utf8_decode('OBTÉN UN RECIBO PERSONALIZADO LA PRÓXIMA VEZ.'), '', 1, 1);

        $this->cursor_y += 14;

        //message
        $this->FPDF->SetXY(43, $this->cursor_y + 12);
        $this->FPDF->SetFont('Helvetica', '', 11);
        $this->FPDF->Cell(5, 5, '', 0, 0);
        // $this->FPDF->Cell(5, 5, '', 0, 0);
        $this->FPDF->Cell(0, 5, utf8_decode('Marca la casilla para elegir lo que verás en tu próximo'), 0, 1);

        $this->FPDF->SetXY(45, $this->cursor_y + 17);
        $this->FPDF->SetFont('Helvetica', '', 11);
        $this->FPDF->Cell(10, 5, '', 0, 0);
        // $this->FPDF->Cell(5, 5, '', 0, 0);
        $this->FPDF->Cell(0, 5, utf8_decode('recibo y déjelo en la caja al lado de la caja. '), 0, 1);


        //checkboxes
        $this->FPDF->SetXY(45, $this->cursor_y + 24);
        $this->FPDF->SetFont('Helvetica', '', 10);
        $this->FPDF->Cell(5, 5, '', 1, 0);
        $this->FPDF->Cell(5, 5, '', 0, 0);
        $this->FPDF->Cell(0, 5, 'Titulares de noticias', 0, 1);

        $this->FPDF->Cell(0, 5, '', 0, 1);

        $this->FPDF->SetXY(45, $this->cursor_y + 30);
        $this->FPDF->Cell(5, 5, '', 1, 0);
        $this->FPDF->Cell(5, 5, '', 0, 0);
        $this->FPDF->Cell(70, 5, 'Mi calendario de Google. Mi Usuario es: ', 0, 0);
        $this->FPDF->Cell(20, 5, '', 1, 0);

        $this->FPDF->SetXY(45, $this->cursor_y += 36);
        $this->FPDF->Cell(5, 5, '', 1, 0);
        $this->FPDF->Cell(5, 5, '', 0, 0);
        $this->FPDF->Cell(60, 5, utf8_decode('20% de descuento en tu próxima comida'), 0, 0);
        $this->FPDF->SetFont('Helvetica', 'B', 11);
        //$this->FPDF->Cell(30, 5, 'Bread & Butter', 0, 1);

        if (count($this->products) >= 3 && count($this->products) <= 4)
            $this->addNewPage();

        $this->FPDF->Cell(0, 3, '', 0, 1);
        // $this->FPDF->Cell(0, 5, '', 0, 1);


        $this->FPDF->Cell(35, 5, '', 0, 1);
        $this->FPDF->Cell(30, 2, '', 0, 1);
        $this->FPDF->Cell(0, 5, '', 0, 1);
        $this->FPDF->Cell(30, 5, '', 0, 0);
        $this->FPDF->SetFont('Helvetica', '', 14);
        $this->FPDF->Cell(37, 5, 'ID DE CLIENTE:', 0, 0);
        $this->FPDF->Cell(45, 5, '046348632', 0, 0);

        $this->cursor_y += 12;

        $this->FPDF->Code128(40, $this->cursor_y + 12, '046348632-5219-21212-52', 110, 10);

        $this->FPDF->SetLineWidth(1);
        $this->FPDF->Line(40, $this->cursor_y + 30, 150, $this->cursor_y + 30);
        $this->FPDF->Line(40, $this->cursor_y + 45, 150, $this->cursor_y + 45);

        /*for ($i = 0; $i < 7; $i++) {
            $this->FPDF->Cell(0, 4, '', 0, 1);
        }*/

        $this->FPDF->SetXY(40, $this->cursor_y + 35);
        $this->FPDF->Cell(0, 6, 'GRACIAS - NOS VEMOS PRONTO.', 0, 1, 'C');
        $this->FPDF->SetLineWidth(0.5);
        $this->FPDF->Cell(0, 5, '', '', 1);
        $this->FPDF->Cell(0, 5, '', 'B', 1);

        $this->cursor_y += 51;

        $this->FPDF->Line(35, $this->cursor_y, 35, 5);
        $this->FPDF->Line(155, $this->cursor_y, 155, 5);

    }

    private function closeBorderLines($y_top = 429, $line_width = 0.5)
    {
        $this->FPDF->SetLineWidth(0.5);
        $this->FPDF->Line(35, 5, 35, 429);
        $this->FPDF->Line(155, 5, 155, 429);

    }

    private function new_blank_line($space, $border, $line_width)
    {
        $this->FPDF->SetLineWidth($line_width);
        $this->FPDF->Cell(0, $space, '', $border, 1);
    }

}