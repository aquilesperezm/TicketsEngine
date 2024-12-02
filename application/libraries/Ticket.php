<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');
require_once('Fpdf.php');

class Ticket
{

    private $title;
    private $title_font_size;
    private $title_align;
    private $details;
    private $address;
    private $order_number;
    private $client_number;
    private $iva;
    private $price_phrase;
    private $offer_title;
    private $offer_text;
    //stores
    private $products;
    private $offers;


    //----------------------------------------------------- Core Properties --------------------------------------------

    private $PDF;
    private $cursor_x;
    private $cursor_y;

    //----------------------------------------------------- Core Properties --------------------------------------------
    /**
     * @var int|mixed
     */
    private $details_font_size;
    /**
     * @var mixed|string
     */
    private $details_align;

    /**
     * @param array|null $array
     * @example
     *      $array = [
     *           'title' => 'Bread & Butter',
     *           'details' => 'LUNES 15/04/2011, MASTERCARD *3452, EXP 04/03, 12. 15:00 O ALMUERZO: £ 5,14',
     *           'address' => '32 GREAT EASTERN STREET, LONDON',
     *           'order_number'=>'2049',
     *           'iva'=> '1.03',
     *           'client_number'=>'046348632',
     *           'price_phrase'=>'IN 514AD, VITALIUS LEADS A REBELLION IN THE BIZANTINE EMPIRE.',
     *           'offer_title'=>'¿ERES UN CLIENTE REGULAR?',
     *           'offer_text'=>'Marca la casilla para elegir lo que verás en tu próximo',
     *           'offers'=>[
     *                  'add_1'=>[
     *                      'title'=>'Titulares de noticias'
     *                      ],
     *                  'add_2'=>[
     *                       'title'=>'Mi calendario de Google. Mi Usuario es',
     *                       'field_size'=>20
     *                       ],
     *                   'add_3'=>[
     *                        'title'=>'20% de descuento en tu próxima comida'
     *                        ]
     *                  ],
     *            'products'=>[
     *              [
     *                      'nombre' => 'Ham & Cheese Baguette',
     *                      'cantidad' => 1,
     *                      'precio' => 2.79,
     *                      'hora' => '2pm',
     *                      'graph_message' => 'Which often sells out by ',
     *                      'rda' => 57,
     *                      'calorias' => 120,
     *                      'sales' => [
     *                                       '01-05' => 5,
     *                                       '05-10' => 7,
     *                                       '10-15' => 12,
     *                                       '15-20' => 17,
     *                                       '20-25' => 21,
     *                                       '25-30' => 29
     *                                   ]
     *
     *               ],
     *              [
     *                      'nombre' => 'Chicken Caesar Salad',
     *                      'cantidad' => 1,
     *                      'precio' => 2.79,
     *                      'hora' => '2pm',
     *                      'graph_message' => 'Which often sells out by ',
     *                      'rda' => 38,
     *                      'calorias' => 206,,
     *                      'sales' => [
     *                                        '01-05' => 5,
     *                                        '05-10' => 7,
     *                                        '10-15' => 12,
     *                                        '15-20' => 17,
     *                                        '20-25' => 21,
     *                                        '25-30' => 29
     *                                    ]
     *
     *              ],
     *              [
     *              'nombre' => 'Grilled Cheese Sandwich',
     *              'cantidad' => 1,
     *              'precio' => 2.79,
     *              'hora' => '2pm',
     *              'graph_message' => 'Which often sells out by ',
     *              'rda' => 25,
     *              'calorias' => 134,
     *              'sales' => [
     * 'Sales 4' => [
     * '01-05' => rand($min, $max),
     * '05-10' => rand($min, $max),
     * '10-15' => rand($min, $max),
     * '15-20' => rand($min, $max),
     * '20-25' => rand($min, $max),
     * '25-30' => rand($min, $max)
     * ]
     * ]
     *          ],
     *      ]
     *
     */
    public function __construct(array $array = null)
    {

        $this->products = array();
        $this->offers = array();


        $this->PDF = new Fpdf('P');

        $this->PDF->SetMargins(35, 0, 35);
        $this->addNewPage();

        $this->PDF->SetFont('Helvetica', 'B', 12);

        $this->products[] = [
            'nombre' => 'Ham & Cheese Baguette',
            'cantidad' => 1,
            'precio' => $this->float_rand(1.0, 10.0, 2),
            'hora' => '2pm',
            'graph_message' => 'Which often sells out by ',
            'rda' => 57,
            'calorias' => 120,
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
        ];

        $this->products[] = [
            'nombre' => 'Chicken Caesar Salad',
            'cantidad' => 1,
            'precio' => $this->float_rand(1.0, 10.0, 2),
            'hora' => '1pm',
            'graph_message' => 'Which often sells out by ',
            'rda' => 57,
            'calorias' => 120,
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
        ];

    }

    private function float_rand($Min, $Max, $round = 0)
    {
        //validate input
        if ($Min > $Max) {
            $min = $Max;
            $max = $Min;
        } else {
            $min = $Min;
            $max = $Max;
        }
        $randomfloat = $min + mt_rand() / mt_getrandmax() * ($max - $min);
        if ($round > 0)
            $randomfloat = round($randomfloat, $round);

        return $randomfloat;
    }

    private function addNewPage($cursor_x = 35, $cursor_y = 5)
    {

        $this->PDF->AddPage('P', array(190, 450));
        $this->cursor_y = $cursor_y;
        $this->cursor_x = $cursor_x;

    }

    private function generateRandomValue($min, $max, array $exclude = array())
    {
        $range = array_diff(range($min, $max), $exclude);
        return array_shift($range);
    }

    public function set_details($details, $font_size = 12, $align = "C")
    {
        $this->details = $details;
        $this->details_font_size = $font_size;
        $this->details_align = $align;
    }

    public function set_title($title, $font_size = 12, $align = 'L')
    {
        $this->title = $title;
        $this->title_font_size = $font_size;
        $this->title_align = $align;

    }

    public function set_address($address)
    {
        $this->address = $address;

    }

    public function set_order_number($order_number)
    {
        $this->order_number = $order_number;

    }

    public function set_client_number($client_number)
    {
        $this->client_number = $client_number;

    }

    public function set_iva($iva)
    {
        $this->iva = $iva;

    }

    public function set_price_phrase($price_phrase)
    {
        $this->price_phrase = $price_phrase;

    }

    public function set_offer_title($offer_title)
    {
        $this->offer_title = $offer_title;

    }

    public function set_offer_text($offer_text)
    {
        $this->offer_text = $offer_text;

    }

    public function add_offer($offer)
    {
        $this->offers[] = $offer;
    }


    public function add_product($product)
    {
        $this->products[] = $product;
    }

    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @return string
     */
    public function getDetails()
    {
        return $this->details;
    }

    /**
     * @return string
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * @return string
     */
    public function getOrderNumber()
    {
        return $this->order_number;
    }

    /**
     * @return string
     */
    public function getClientNumber()
    {
        return $this->client_number;
    }

    /**
     * @return string
     */
    public function getIva()
    {
        return $this->iva;
    }

    /**
     * @return string
     */
    public function getPricePhrase()
    {
        return $this->price_phrase;
    }

    /**
     * @return string
     */
    public function getOfferTitle()
    {
        return $this->offer_title;
    }

    /**
     * @return string
     */
    public function getOfferText()
    {
        return $this->offer_text;
    }

    /**
     * @return mixed
     */
    public function getProducts()
    {
        return $this->products;
    }

    /**
     * @return mixed
     */
    public function getOffers()
    {
        return $this->offers;
    }


    private function new_blank_line($space, $border, $line_width)
    {
        $this->PDF->SetLineWidth($line_width);
        $this->PDF->Cell(0, $space, '', $border, 1);
    }

    private function generate_first_section()
    {
        $this->new_blank_line(5, 'B', 0.5);
        $this->new_blank_line(5, '', 0.5);

        $this->PDF->SetDrawColor(169, 169, 169);
        $this->PDF->SetDash(1, 1);
        $this->PDF->Cell(20, 5, '', 'TR', 0);
        $this->PDF->SetDash();
        $this->PDF->SetDrawColor(0, 0, 0);
        $this->PDF->Cell(5, 5, '', 0, 0);
        $this->PDF->Cell(90, 5, '', 'LTR', 0);
        $this->PDF->Cell(0, 5, '', '', 1);

        for ($i = 0; $i < 4; $i++) {

            $this->PDF->SetDash(1, 1);
            $this->PDF->SetDrawColor(169, 169, 169);
            $this->PDF->Cell(20, 5, '', 'LR', 0);
            $this->PDF->SetDash();
            $this->PDF->SetDrawColor(0, 0, 0);
            $this->PDF->Cell(5, 5, '', 0, 0);
            $this->PDF->Cell(90, 5, '', 'LR', 0);
            $this->PDF->Cell(0, 5, '', '', 1);
        }

        $this->PDF->SetDash(1, 1);
        $this->PDF->SetDrawColor(169, 169, 169);
        $this->PDF->Cell(20, 5, '', 'LBR', 0);
        $this->PDF->SetDash();
        $this->PDF->SetDrawColor(0, 0, 0);
        $this->PDF->Cell(5, 5, '', 0, 0);
        $this->PDF->Cell(90, 5, '', 'LR', 0);
        $this->PDF->Cell(0, 5, '', '', 1);

        $this->PDF->Cell(20, 5, '', '', 0);
        $this->PDF->Cell(5, 5, '', 0, 0);
        $this->PDF->Cell(90, 5, '', 'LR', 0);
        $this->PDF->Cell(0, 5, '', '', 1);

        $this->PDF->Cell(20, 5, '', '', 0);
        $this->PDF->Cell(5, 5, '', 0, 0);
        $this->PDF->Cell(90, 5, '', 'LR', 0);
        $this->PDF->Cell(0, 5, '', '', 1);

        $this->PDF->Cell(20, 5, '', '', 0);
        $this->PDF->Cell(5, 5, '', 0, 0);
        $this->PDF->Cell(90, 5, '', 'LR', 0);
        $this->PDF->Cell(0, 5, '', '', 1);

        $this->PDF->Cell(20, 5, '', '', 0);
        $this->PDF->Cell(5, 5, '', 0, 0);
        $this->PDF->Cell(90, 5, '', 'LR', 0);
        $this->PDF->Cell(0, 5, '', '', 1);

        $this->PDF->Cell(20, 5, '', '', 0);
        $this->PDF->Cell(5, 5, '', 0, 0);
        $this->PDF->Cell(90, 5, '', 'LBR', 0);
        $this->PDF->Cell(0, 5, '', '', 1);

        $this->PDF->SetDash();
        $this->PDF->SetDrawColor(0, 0, 0);
        $this->PDF->Line(35, 40, 35, 10);

        $this->PDF->Cell(0, 5, '', '', 1);

        $this->PDF->SetDash(1, 1);
        $this->PDF->SetDrawColor(169, 169, 169);
        $this->PDF->Line(35, 70, 155, 70);
        $this->PDF->SetDash();
        $this->PDF->SetDrawColor(0, 0, 0);

        $this->PDF->SetTextColor(169, 169, 169);
        $this->PDF->SetFont('Helvetica', 'B', 9);
        $this->PDF->TextWithRotation(45, 35, 'STAPLE/CUP', 90);

        $this->PDF->SetFont('Helvetica', 'B', 7);
        $this->PDF->Text(63, 15, 'DETALLES');
        $this->PDF->SetDrawColor(169, 169, 169);
        $this->PDF->Line(62, 17, 145, 17);

        $this->PDF->SetTextColor(0, 0, 0);
        $this->PDF->SetFont('Helvetica', 'B', 14);

        /* $this->PDF->SetXY(64, 20);
         $this->PDF->WriteText('<LUNES> 15/04/2011,');

         $this->PDF->SetXY(64, 26);
         $this->PDF->WriteText('<MASTERCARD *3452, EXP 04/03,>');

         $this->PDF->SetXY(64, 32);
         $this->PDF->WriteText('<12. 15:00> O <' . utf8_decode('ALMUERZO: £ 5,14') . '>');*/

        $this->PDF->SetXY(64, 40);
        $this->PDF->SetDrawColor(169, 169, 169);
        $this->PDF->SetTextColor(169, 169, 169);
        $this->PDF->SetFont('Helvetica', 'B', 7);
        $this->PDF->Cell(82, 5, 'NOTAS', 'LTR', 1);
        $this->PDF->SetXY(64, 40);
        $this->PDF->Cell(82, 20, '', 1);

        $this->PDF->SetXY(35, 70);

        $this->cursor_x = 35;
        $this->cursor_y = 70;

        //render

        $this->render_details(FALSE);

    }

    private function generate_second_section()
    {
        $this->PDF->SetDrawColor(0, 0, 0);
        $this->PDF->Cell(120, 5, '', '', 1, 1);

        $this->PDF->SetLineWidth(2);
        $this->PDF->Line(40, 75, 150, 75);

        //border

        /*$border_length = 12 + count($this->productos) * 12;

        $this->PDF->SetLineWidth(0.5);
        for ($i = 0; $i < $border_length; $i++) {
            $this->PDF->Cell(120, 10, '', 'LR', 1, 1);
        }*/

        $this->PDF->SetTextColor(0, 0, 0);
        $sample_text = '';

        // ----------------------------------------------- old title ----------------------------------------------------
        /*  $test1 = array();
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

          $distance_points = 40;
          $this->PDF->SetXY($distance_points, 63);
          $this->PDF->SetFont('Helvetica', 'B', 28);
          $this->PDF->MultiCellBltArray(60, 46, $test1);

          // Title of the ticket
          /*$this->PDF->SetFont('Helvetica', '', 20);
          $this->PDF->Text(90, 88, '&');
          $this->PDF->SetFont('Helvetica', 'B', 30);
          $this->PDF->Text(96, 89, 'BUTTER');

         // $this->PDF->SetXY($distance_points + 8, 83);
         // $this->PDF->WriteText('<BREAD> & <BUTTER>');
         // $this->PDF->WriteText($this->title);

          $this->PDF->SetFont('Helvetica', 'B', 30);
          $this->PDF->SetXY(140, 81);
          $this->PDF->MultiCellBltArray(10, 10, $test2);*/
//----------------------------------------------------- old title ---------------------------------------------------


        $this->PDF->SetXY(140, 90);
        $this->PDF->SetLineWidth(1);
        $this->PDF->Line(45, 95, 143, 95);

        $this->PDF->SetFont('Helvetica', '', 10);
        //$this->PDF->SetXY(140,100);
        $this->PDF->SetXY(53, 99);
        $this->PDF->WriteText('32 GREAT EASTERN STREET, LONDON, EC2A 4RQ');

        $this->PDF->SetXY(50, 104);
        $this->PDF->WriteText('BREADBUTTER.COM | 020 8888 8888 | VAT 333 3333 33');

        //$this->PDF->Text(53, 103, '32 GREAT EASTERN STREET, LONDON, EC2A 4RQ');
        //$this->PDF->Text(50, 108, 'BREADBUTTER.COM | 020 8888 8888 | VAT 333 3333 33');

        $this->PDF->SetLineWidth(2);
        $this->PDF->Line(40, 114, 150, 114);
        $this->PDF->SetFont('Helvetica', '', 8);
        $this->PDF->Text(40, 119, 'NO. ORDEN: ');
        $this->PDF->SetFont('Helvetica', 'B', 8);
        $this->PDF->Text(57, 119, '2049');

        $this->PDF->Image('assets/img/banner11.png', 65, 117);

        $this->PDF->SetFont('Helvetica', 'B', 40);
        $this->PDF->Text(80, 140, utf8_decode('£5.14'));
        $this->PDF->SetFont('Helvetica', '', 10);
        $this->PDF->Text(73, 153, 'INCLUYE IVA DE ');
        $this->PDF->SetFont('Helvetica', 'BU', 10);
        $this->PDF->Text(103, 153, utf8_decode('£1.03'));

        $this->PDF->Image('assets/img/banner22.png', 37, 147);

        $this->PDF->SetFont('Helvetica', 'B', 12);
        $this->PDF->TextWithRotation(43, 158, 'PRICE', 15);
        $this->PDF->TextWithRotation(45, 165, 'FACT!', 15);


        /*$this->PDF->SetFont('Helvetica', '', 11);
        $this->PDF->SetXY(65, 165);
        $this->PDF->Cell(180, 5, 'IN ', '0');
        $this->PDF->SetFont('Helvetica', 'B', 11);
        $this->PDF->SetXY(70, 165);
        $this->PDF->Cell(190, 5, '514', '0');
        $this->PDF->SetFont('Helvetica', '', 11);
        $this->PDF->SetXY(77, 165);
        $this->PDF->Cell(203, 5, 'AD, VITALIUS LEADS A ', '0');
        $this->PDF->SetXY(58, 170);
        $this->PDF->Cell(170, 5, 'REBELLION IN THE BIZANTINE EMPIRE.', '0');*/

        $this->PDF->SetFont('Helvetica', '', 12);
        $this->PDF->SetXY(67, 165);
        $this->PDF->WriteText('IN <514>AD, VITALIUS LEADS A');
        $this->PDF->SetXY(56, 172);
        $this->PDF->WriteText('REBELLION IN THE BIZANTINE EMPIRE.');


        //$this->PDF->SetXY(60,180);
        $this->PDF->SetFont('Helvetica', 'BU', 11);
        $this->PDF->Text(80, 190, 'Y COMPRASTE:', 1);

        $this->render_title(FALSE);
    }

    private function generate_products()
    {

        $this->PDF->SetLineWidth(0.5);
        $this->PDF->SetXY(40, 195);
        $this->PDF->SetFont('Helvetica', '', 12);

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
                    $this->PDF->SetXY($block_x, $this->cursor_y);
                    $this->PDF->SetFont('Helvetica', '', 12);
                    $this->PDF->WriteText('<' . $this->products[$i]['cantidad'] . '> @ ' . utf8_decode('£' . $this->products[$i]['precio']));

                    $this->PDF->SetXY($block_x - 10, $this->cursor_y + 5);
                    $sales = $this->products[$i]['sales'];
                    $graph_color = $this->products[$i]['graph_color'];
                    $this->PDF->LineGraph(70, 20, $sales, 'HV', $graph_color, 10, 7, FALSE, FALSE, FALSE);

                    // $this->PDF->Image('assets/img/product_graph_1.png', $block_x, $this->cursor_y + 5);
                    $this->PDF->SetFont('Helvetica', '', 12);
                    $this->PDF->SetXY($block_x, $this->cursor_y + 20);
                    $this->PDF->WriteText('<' . $this->products[$i]['nombre'] . '>');
                    $this->PDF->SetXY($block_x, $this->cursor_y + 25);
                    $this->PDF->SetFont('Helvetica', '', 10);
                    $this->PDF->WriteText($this->products[$i]['graph_message'] . $this->products[$i]['hora']);

                } else {

                    $block_x = 40;
                    $this->PDF->SetXY($block_x, $this->cursor_y);
                    $this->PDF->SetFont('Helvetica', '', 12);
                    $this->PDF->WriteText('<' . $this->products[$i]['cantidad'] . '> @ ' . utf8_decode('£' . $this->products[$i]['precio']));

                    $this->PDF->SetXY($block_x - 10, $this->cursor_y + 5);
                    $sales = $this->products[$i]['sales'];
                    $graph_color = $this->products[$i]['graph_color'];
                    $this->PDF->LineGraph(70, 20, $sales, 'HV', $graph_color, 10, 7, FALSE, FALSE, FALSE);

                    // $this->PDF->Image('assets/img/product_graph_1.png', $block_x, $this->cursor_y + 5);
                    $this->PDF->SetFont('Helvetica', '', 12);
                    $this->PDF->SetXY($block_x, $this->cursor_y + 20);
                    $this->PDF->WriteText('<' . $this->products[$i]['nombre'] . '>');
                    $this->PDF->SetXY($block_x, $this->cursor_y + 25);
                    $this->PDF->SetFont('Helvetica', '', 10);
                    $this->PDF->WriteText($this->products[$i]['graph_message'] . $this->products[$i]['hora']);
                }


            } else {

                $block_x += 60;
                $this->PDF->SetXY($block_x, $this->cursor_y);
                $this->PDF->SetFont('Helvetica', '', 12);
                $this->PDF->WriteText('<' . $this->products[$i]['cantidad'] . '> @ ' . utf8_decode('£' . $this->products[$i]['precio']));

                $this->PDF->SetXY($block_x - 10, $this->cursor_y + 5);
                $sales = $this->products[$i]['sales'];
                $graph_color = $this->products[$i]['graph_color'];
                $this->PDF->LineGraph(70, 20, $sales, 'HV', $graph_color, 10, 7, FALSE, FALSE, FALSE);

                //   $this->PDF->Image('assets/img/product_graph_1.png', $block_x, $this->cursor_y + 5);
                $this->PDF->SetFont('Helvetica', '', 12);
                $this->PDF->SetXY($block_x, $this->cursor_y + 20);
                $this->PDF->WriteText('<' . $this->products[$i]['nombre'] . '>');
                $this->PDF->SetXY($block_x, $this->cursor_y + 25);
                $this->PDF->SetFont('Helvetica', '', 10);
                $this->PDF->WriteText($this->products[$i]['graph_message'] . $this->products[$i]['hora']);

                $this->cursor_y += 35;

            }

        }

        $this->cursor_x = 36;


    }

    private function generate_third_section()
    {

        $this->closeBorderLines();

        if (count($this->products) >= 9 && count($this->products) <= 12) {
            $this->addNewPage(35, 5);
        }

        if (count($this->products) % 2 != 0 && (count($this->products) < 9 || count($this->products) > 12))
            $this->cursor_y += 35;

        $this->PDF->Image('assets/img/banner4.png', $this->cursor_x, $this->cursor_y);

        $this->PDF->SetXY(46, $this->cursor_y += 13);
        $this->PDF->SetFont('Helvetica', '', 9);
        $this->PDF->Cell(50, 5, 'EN TOTAL, ESTO SUMA : ', 0, 1);

        $this->PDF->SetXY(113, $this->cursor_y += 2);
        $this->PDF->SetFont('Helvetica', '', 9);
        $this->PDF->Cell(50, 5, 'O APROXIM.: ', 0, 0);

        $this->PDF->SetFont('Helvetica', 'B', 40);
        $this->PDF->SetXY(53, $this->cursor_y += 8);
        $this->PDF->Cell(50, 5, '986', 0, 1);

        $this->PDF->SetXY(111, $this->cursor_y + 5);
        $this->PDF->SetFont('Helvetica', 'B', 40);
        $this->PDF->Cell(50, 5, '45%', 0, 1);

        $this->PDF->SetFont('Helvetica', '', 9);
        $this->PDF->SetXY(57, $this->cursor_y += 11);
        $this->PDF->Cell(50, 5, 'CALORIAS,', 0, 1);

        $this->PDF->SetXY(111, $this->cursor_y + 3);
        $this->PDF->SetFont('Helvetica', '', 9);
        $this->PDF->Cell(50, 5, 'DE TU RDA.', 0, 1);

        $this->PDF->Cell(20, 5, '', 0, 0);

        $this->PDF->SetFont('Helvetica', 'B', 12);
        $this->PDF->TextWithRotation(75, $this->cursor_y += 27, utf8_decode('¿AÚN MÁS TARDE?'), 2);
        $this->PDF->SetFont('Helvetica', '', 11);

        $this->PDF->SetXY(44, $this->cursor_y + 3);
        $this->PDF->WriteTextWithRotation(43, $this->cursor_y + 20, 'The <Nice Gallery> on <Great Eastern Street> is holding its', 2);

        $this->PDF->SetXY(58, $this->cursor_y + 8);
        $this->PDF->WriteTextWithRotation(43, $this->cursor_y + 20, 'opening night from 6pm. <(bit.ly/6h23b)>', 2);

        /* $this->PDF->SetFont('Helvetica', '', 12);
         $this->PDF->TextWithRotation(43, $this->cursor_y + 6, 'The', 2);
         $this->PDF->SetFont('Helvetica', 'B', 12);
         $this->PDF->TextWithRotation(51, $this->cursor_y + 6, 'Nice Gallery', 2);
         $this->PDF->SetFont('Helvetica', '', 12);
         $this->PDF->TextWithRotation(76, $this->cursor_y + 5 + 0.2, 'on', 2);
         $this->PDF->SetFont('Helvetica', 'B', 12);
         $this->PDF->TextWithRotation(81, $this->cursor_y + 5 + 0.2, 'Great Eastern Street', 2);
         $this->PDF->SetFont('Helvetica', '', 12);
         $this->PDF->TextWithRotation(123, $this->cursor_y + 4, 'is holding its', 2);*/

        // $this->PDF->SetXY(40, 255);
        //  $this->PDF->TextWithRotation(58, $this->cursor_y + 11, 'opening night from 6pm.', 2);
        //  $this->PDF->SetFont('Helvetica', 'B', 12);
        //  $this->PDF->TextWithRotation(105, $this->cursor_y + 10, '(bit.ly/6h23b)', 2);


        $this->PDF->SetDash(1, 1);
        $this->PDF->SetDrawColor(169, 169, 169);
        $this->PDF->Line(35, $this->cursor_y + 24, 155, $this->cursor_y + 24);
        $this->PDF->SetDash();

        $this->PDF->Image('assets/img/banner5.png', 45, $this->cursor_y + 20);

        $this->cursor_y += 25;

    }

    private function generate_fourth_section()
    {

        if (count($this->products) >= 5 && count($this->products) <= 8)
            $this->addNewPage(35, 0);


        //border
        $this->PDF->SetLineWidth(0.5);
        $this->PDF->SetDrawColor(0, 0, 0);


        $this->PDF->Image('assets/img/banner6.png', 35, $this->cursor_y + 3);

        $this->PDF->SetXY(40, $this->cursor_y + 9);
        $this->PDF->Cell(30, 5, '', '', 0, 0);
        $this->PDF->SetFont('Helvetica', 'B', 10);
        $this->PDF->Cell(40, 4, utf8_decode('¿ERES UN CLIENTE REGULAR?'), '', 1, 1);
        $this->PDF->SetXY(30, $this->cursor_y + 14);
        $this->PDF->Cell(15, 5, '', '', 0, 0);
        $this->PDF->SetFont('Helvetica', '', 10);
        $this->PDF->Cell(45, 5, utf8_decode('OBTÉN UN RECIBO PERSONALIZADO LA PRÓXIMA VEZ.'), '', 1, 1);

        $this->cursor_y += 14;

        //message
        $this->PDF->SetXY(43, $this->cursor_y + 12);
        $this->PDF->SetFont('Helvetica', '', 11);
        $this->PDF->Cell(5, 5, '', 0, 0);
        // $this->PDF->Cell(5, 5, '', 0, 0);
        $this->PDF->Cell(0, 5, utf8_decode('Marca la casilla para elegir lo que verás en tu próximo'), 0, 1);

        $this->PDF->SetXY(45, $this->cursor_y + 17);
        $this->PDF->SetFont('Helvetica', '', 11);
        $this->PDF->Cell(10, 5, '', 0, 0);
        // $this->PDF->Cell(5, 5, '', 0, 0);
        $this->PDF->Cell(0, 5, utf8_decode('recibo y déjelo en la caja al lado de la caja. '), 0, 1);


        //checkboxes
        $this->PDF->SetXY(45, $this->cursor_y + 24);
        $this->PDF->SetFont('Helvetica', '', 10);
        $this->PDF->Cell(5, 5, '', 1, 0);
        $this->PDF->Cell(5, 5, '', 0, 0);
        $this->PDF->Cell(0, 5, 'Titulares de noticias', 0, 1);

        $this->PDF->Cell(0, 5, '', 0, 1);

        $this->PDF->SetXY(45, $this->cursor_y + 30);
        $this->PDF->Cell(5, 5, '', 1, 0);
        $this->PDF->Cell(5, 5, '', 0, 0);
        $this->PDF->Cell(70, 5, 'Mi calendario de Google. Mi Usuario es: ', 0, 0);
        $this->PDF->Cell(20, 5, '', 1, 0);

        $this->PDF->SetXY(45, $this->cursor_y += 36);
        $this->PDF->Cell(5, 5, '', 1, 0);
        $this->PDF->Cell(5, 5, '', 0, 0);
        $this->PDF->Cell(60, 5, utf8_decode('20% de descuento en tu próxima comida'), 0, 0);
        $this->PDF->SetFont('Helvetica', 'B', 11);
        //$this->PDF->Cell(30, 5, 'Bread & Butter', 0, 1);

        if (count($this->products) >= 3 && count($this->products) <= 4)
            $this->addNewPage();

        $this->PDF->Cell(0, 3, '', 0, 1);
        // $this->PDF->Cell(0, 5, '', 0, 1);


        $this->PDF->Cell(35, 5, '', 0, 1);
        $this->PDF->Cell(30, 2, '', 0, 1);
        $this->PDF->Cell(0, 5, '', 0, 1);
        $this->PDF->Cell(30, 5, '', 0, 0);
        $this->PDF->SetFont('Helvetica', '', 14);
        $this->PDF->Cell(37, 5, 'ID DE CLIENTE:', 0, 0);
        $this->PDF->Cell(45, 5, '046348632', 0, 0);

        $this->cursor_y += 12;

        $this->PDF->Code128(40, $this->cursor_y + 12, '046348632-5219-21212-52', 110, 10);

        $this->PDF->SetLineWidth(1);
        $this->PDF->Line(40, $this->cursor_y + 30, 150, $this->cursor_y + 30);
        $this->PDF->Line(40, $this->cursor_y + 45, 150, $this->cursor_y + 45);

        /*for ($i = 0; $i < 7; $i++) {
            $this->PDF->Cell(0, 4, '', 0, 1);
        }*/

        $this->PDF->SetXY(40, $this->cursor_y + 35);
        $this->PDF->Cell(0, 6, 'GRACIAS - NOS VEMOS PRONTO.', 0, 1, 'C');
        $this->PDF->SetLineWidth(0.5);
        $this->PDF->Cell(0, 5, '', '', 1);
        $this->PDF->Cell(0, 5, '', 'B', 1);

        $this->cursor_y += 51;

        $this->PDF->Line(35, $this->cursor_y, 35, 5);
        $this->PDF->Line(155, $this->cursor_y, 155, 5);

    }

    private function closeBorderLines($y_top = 429, $line_width = 0.5)
    {
        $this->PDF->SetLineWidth(0.5);
        $this->PDF->Line(35, 5, 35, 429);
        $this->PDF->Line(155, 5, 155, 429);

    }

    private function render_details($border = '')
    {

        $this->PDF->SetDrawColor(0, 0, 0);
        $this->PDF->SetTextColor(0, 0, 0);
        $this->PDF->SetXY(63, 18);
        $this->PDF->SetStyle2("b", "Helvetica", "B", $this->details_font_size, "0,0,0");
        $this->PDF->SetFont('Helvetica', '', $this->details_font_size);
        $this->PDF->MultiCellTag(83, 7, $this->details, $border, $this->details_align, 0);

    }

    private function render_title($border)
    {

        $bullet = chr(149);
        $this->PDF->SetLineWidth(0.5);
        $this->PDF->SetDrawColor(255, 0, 0);
        $this->PDF->SetXY(35, 76);
        $this->PDF->SetStyle2("b", "Helvetica", "B", $this->title_font_size, "0,0,0");
        $this->PDF->SetStyle2("b1", "Helvetica", "", $this->title_font_size - 10, "0,0,0");
        $this->PDF->SetFont('Helvetica', '', $this->title_font_size);
        $this->PDF->MultiCellTag(120, 18, $bullet . "   " . $this->title . "   " . $bullet, $border, $this->title_align, 0);
        $this->PDF->SetDrawColor(0, 0, 0);

    }


    public function render_ticket_pdf()
    {
        $this->generate_first_section();
        $this->generate_second_section();
        $this->generate_products();
        $this->generate_third_section();
        $this->generate_fourth_section();
        $this->PDF->Output('I', 'ticket_temp.pdf');

    }

}