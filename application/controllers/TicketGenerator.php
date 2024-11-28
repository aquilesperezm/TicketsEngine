<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

class TicketGenerator extends CI_Controller
{

    private $FPDF;
    private $pound_sterling;

    public function __construct()
    {
        parent::__construct();

        $params = array('P', 'in');
        $this->load->library('fpdf', $params);

        $this->FPDF = $this->fpdf;
        $this->pound_sterling = substr('£', 1, 1);


    }

    public function generateTicket()
    {
        $this->FPDF->SetMargins(35, 0, 35);
        $this->FPDF->AddPage('P', array(190, 450));

        $this->FPDF->SetFont('Helvetica', 'B', 12);

        $this->section_1();
        $this->section_2();
        $this->section_3();


        $this->FPDF->Output('I', 'report.pdf');

    }

    private function section_1()
    {

        //$this->FPDF->Image('image1.jpg');

        $this->new_blank_line(5, 'B', 0.5);
        $this->new_blank_line(5, 'LR', 0.5);

        $this->FPDF->SetDrawColor(169, 169, 169);
        $this->FPDF->SetDash(1, 1);
        $this->FPDF->Cell(20, 5, '', 'LTR', 0);
        $this->FPDF->SetDash();
        $this->FPDF->SetDrawColor(0, 0, 0);
        $this->FPDF->Cell(5, 5, '', 0, 0);
        $this->FPDF->Cell(90, 5, '', 'LTR', 0);
        $this->FPDF->Cell(0, 5, '', 'R', 1);

        for ($i = 0; $i < 4; $i++) {

            $this->FPDF->SetDash(1, 1);
            $this->FPDF->SetDrawColor(169, 169, 169);
            $this->FPDF->Cell(20, 5, '', 'LR', 0);
            $this->FPDF->SetDash();
            $this->FPDF->SetDrawColor(0, 0, 0);
            $this->FPDF->Cell(5, 5, '', 0, 0);
            $this->FPDF->Cell(90, 5, '', 'LR', 0);
            $this->FPDF->Cell(0, 5, '', 'R', 1);
        }

        $this->FPDF->SetDash(1, 1);
        $this->FPDF->SetDrawColor(169, 169, 169);
        $this->FPDF->Cell(20, 5, '', 'LBR', 0);
        $this->FPDF->SetDash();
        $this->FPDF->SetDrawColor(0, 0, 0);
        $this->FPDF->Cell(5, 5, '', 0, 0);
        $this->FPDF->Cell(90, 5, '', 'LR', 0);
        $this->FPDF->Cell(0, 5, '', 'R', 1);

        $this->FPDF->Cell(20, 5, '', 'L', 0);
        $this->FPDF->Cell(5, 5, '', 0, 0);
        $this->FPDF->Cell(90, 5, '', 'LR', 0);
        $this->FPDF->Cell(0, 5, '', 'R', 1);

        $this->FPDF->Cell(20, 5, '', 'L', 0);
        $this->FPDF->Cell(5, 5, '', 0, 0);
        $this->FPDF->Cell(90, 5, '', 'LR', 0);
        $this->FPDF->Cell(0, 5, '', 'R', 1);

        $this->FPDF->Cell(20, 5, '', 'L', 0);
        $this->FPDF->Cell(5, 5, '', 0, 0);
        $this->FPDF->Cell(90, 5, '', 'LR', 0);
        $this->FPDF->Cell(0, 5, '', 'R', 1);

        $this->FPDF->Cell(20, 5, '', 'L', 0);
        $this->FPDF->Cell(5, 5, '', 0, 0);
        $this->FPDF->Cell(90, 5, '', 'LR', 0);
        $this->FPDF->Cell(0, 5, '', 'R', 1);

        $this->FPDF->Cell(20, 5, '', 'L', 0);
        $this->FPDF->Cell(5, 5, '', 0, 0);
        $this->FPDF->Cell(90, 5, '', 'LBR', 0);
        $this->FPDF->Cell(0, 5, '', 'R', 1);

        $this->FPDF->SetDash();
        $this->FPDF->SetDrawColor(0, 0, 0);
        $this->FPDF->Line(35, 40, 35, 10);

        $this->FPDF->Cell(0, 5, '', 'RL', 1);

        $this->FPDF->SetDash(1, 1);
        $this->FPDF->SetDrawColor(169, 169, 169);
        $this->FPDF->Line(35, 70, 155, 70);
        $this->FPDF->SetDash();
        $this->FPDF->SetDrawColor(0, 0, 0);

        $this->FPDF->SetTextColor(169, 169, 169);
        $this->FPDF->SetFont('Helvetica', 'B', 9);
        $this->FPDF->TextWithRotation(45, 35, 'STAPLE/CUP', 90);

        $this->FPDF->SetFont('Helvetica', 'B', 7);
        $this->FPDF->Text(63, 15, 'DETAILS');
        $this->FPDF->SetDrawColor(169, 169, 169);
        $this->FPDF->Line(62, 17, 145, 17);

        $this->FPDF->SetTextColor(0, 0, 0);
        $this->FPDF->SetFont('Helvetica', 'B', 14);

        $this->FPDF->Text(63, 25, 'MONDAY');
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

        $this->FPDF->Text(93, 35, 'LUNCHTIME, ' . $this->pound_sterling . '5.14');

        $this->FPDF->SetXY(64, 40);
        $this->FPDF->SetDrawColor(169, 169, 169);
        $this->FPDF->SetTextColor(169, 169, 169);
        $this->FPDF->SetFont('Helvetica', 'B', 7);
        $this->FPDF->Cell(82, 5, 'NOTES', 'LTR', 1);
        $this->FPDF->SetXY(64, 40);
        $this->FPDF->Cell(82, 20, '', 1);

        $this->FPDF->SetXY(35, 70);


    }

    private function section_2()
    {

        $this->FPDF->SetDrawColor(0, 0, 0);
        $this->FPDF->Cell(120, 5, '', 'LR', 1, 1);

        $this->FPDF->SetLineWidth(2);
        $this->FPDF->Line(40, 75, 150, 75);

        //border
        $this->FPDF->SetLineWidth(0.5);
        for ($i = 0; $i < 24; $i++) {
            $this->FPDF->Cell(120, 10, '', 'LR', 1, 1);
        }

        $this->FPDF->SetTextColor(0, 0, 0);
        $sample_text = 'BREAD';

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

        $this->FPDF->SetXY(45,63);
        $this->FPDF->SetFont('Helvetica', 'B', 30);
        $this->FPDF->MultiCellBltArray(60,46,$test1);

        $this->FPDF->SetFont('Helvetica', '', 20);
        $this->FPDF->Text(90,88,'&');
        $this->FPDF->SetFont('Helvetica', 'B', 30);
        $this->FPDF->Text(96,89,'BUTTER');

        $this->FPDF->SetXY(140,81);
        $this->FPDF->MultiCellBltArray(10,10,$test2);

        $this->FPDF->SetXY(140,90);
        $this->FPDF->Line(45, 95, 143, 95);

        $this->FPDF->SetFont('Helvetica', '', 10);
        //$this->FPDF->SetXY(140,100);
        $this->FPDF->Text(53,103,'32 GREAT EASTERN STREET, LONDON, EC2A 4RQ');
        $this->FPDF->Text(50,108,'BREADBUTTER.COM | 020 8888 8888 | VAT 333 3333 33');

        $this->FPDF->SetLineWidth(2);
        $this->FPDF->Line(40, 114, 150, 114);
        $this->FPDF->SetFont('Helvetica', '', 8);
        $this->FPDF->Text(40,119,'ORDER: ');
        $this->FPDF->SetFont('Helvetica', 'B', 8);
        $this->FPDF->Text(52,119,'2049');

        $this->FPDF->Image('assets/img/banner1.png',65,117);

        $this->FPDF->SetFont('Helvetica', 'B', 40);
        $this->FPDF->Text(80,140,$this->pound_sterling.'5.14');
        $this->FPDF->SetFont('Helvetica', '', 10);
        $this->FPDF->Text(73,153,'INCLUDING VATS OF ');
        $this->FPDF->SetFont('Helvetica', 'BU', 10);
        $this->FPDF->Text(110,153,$this->pound_sterling.'1.03');

        $this->FPDF->Image('assets/img/banner2.png',37,147);

        $this->FPDF->SetFont('Helvetica', '', 11);
        $this->FPDF->SetXY(65,165);
        $this->FPDF->Cell(180,5,'IN ','0');
        $this->FPDF->SetFont('Helvetica', 'B', 11);
        $this->FPDF->SetXY(70,165);
        $this->FPDF->Cell(190,5,'514','0');
        $this->FPDF->SetFont('Helvetica', '', 11);
        $this->FPDF->SetXY(77,165);
        $this->FPDF->Cell(203,5,'AD, VITALIUS LEADS A ','0');
        $this->FPDF->SetXY(58,170);
        $this->FPDF->Cell(170,5,'REBELLION IN THE BIZANTINE EMPIRE.','0');

        //$this->FPDF->SetXY(60,180);
        $this->FPDF->SetFont('Helvetica', 'BU', 11);
        $this->FPDF->Text(80,190,'AND YOU BOUGHT:',1);


        $this->FPDF->SetLineWidth(0.5);
        $this->FPDF->SetXY(40,195);
        $this->FPDF->SetFont('Helvetica', 'B', 12);
        $this->FPDF->Cell(3,5,'1',0);
        $this->FPDF->SetFont('Helvetica', '', 12);
        $this->FPDF->Cell(56,5,'@ '.$this->pound_sterling.'2.79',0,0);
        $this->FPDF->SetFont('Helvetica', 'B', 12);
        $this->FPDF->Cell(3,5,'1',0);
        $this->FPDF->SetFont('Helvetica', '', 12);
        $this->FPDF->Cell(0,5,'@ '.$this->pound_sterling.'2.35',0,1);

        $this->FPDF->Image('assets/img/banner3.png',40,200);

        $this->FPDF->SetXY(40,215);
        $this->FPDF->SetFont('Helvetica', 'B', 12);
        $this->FPDF->Cell(60,5,'Ham & Cheese Baguette',0);
        $this->FPDF->Cell(0,5,'Chocolate Fudge Cake',0,1);
        $this->FPDF->SetXY(40,220);
        $this->FPDF->SetFont('Helvetica', '', 10);
        $this->FPDF->Cell(60,5,'Which often sells out by 2pm.',0);
        $this->FPDF->Cell(0,5,'Which peaks in sales at 1pm',0,1);

        $this->FPDF->Image('assets/img/banner4.png',36,227);

        $this->FPDF->SetXY(43,240);
        $this->FPDF->SetFont('Helvetica', '', 9);
        $this->FPDF->Cell(50,5,'IN TOTAL, THIS ADDS UP TO: ',0,1);
        $this->FPDF->SetFont('Helvetica', 'B', 40);
        $this->FPDF->SetXY(53,250);
        $this->FPDF->Cell(50,5,'986',0,1);
        $this->FPDF->SetFont('Helvetica', '', 9);
        $this->FPDF->SetXY(57,261);
        $this->FPDF->Cell(50,5,'CALORIES,',0,1);
        $this->FPDF->SetXY(111,254);
        $this->FPDF->SetFont('Helvetica', 'B', 40);
        $this->FPDF->Cell(50,5,'45%',0,1);

        $this->FPDF->SetXY(111,262);
        $this->FPDF->SetFont('Helvetica', '', 9);
        $this->FPDF->Cell(50,5,'OF YOUR RDA.',0,1);

        $this->FPDF->Cell(20,5,'',0,0);
        $this->FPDF->SetXY(113,242);
        $this->FPDF->SetFont('Helvetica', '', 9);
        $this->FPDF->Cell(50,5,'OR ROUGHLY: ',0,0);

        $this->FPDF->SetFont('Helvetica', 'B', 12);
        $this->FPDF->SetXY(113,246);
        $this->FPDF->TextWithRotation(75, 287.5, 'STILL AROUND LATER?', 2);

        $this->FPDF->SetXY(40,250);
        $this->FPDF->SetFont('Helvetica', '', 12);
        $this->FPDF->TextWithRotation(43, 293.2, 'The', 2);
        $this->FPDF->SetFont('Helvetica', 'B', 12);
        $this->FPDF->TextWithRotation(51, 293, 'Nice Gallery', 2);
        $this->FPDF->SetFont('Helvetica', '', 12);
        $this->FPDF->TextWithRotation(76, 292.2, 'on', 2);
        $this->FPDF->SetFont('Helvetica', 'B', 12);
        $this->FPDF->TextWithRotation(81, 292.2, 'Great Eastern Street', 2);
        $this->FPDF->SetFont('Helvetica', '', 12);
        $this->FPDF->TextWithRotation(123, 291.2, 'is holding its', 2);

        $this->FPDF->SetXY(40,255);
        $this->FPDF->TextWithRotation(58, 298, 'opening night from 6pm.', 2);
        $this->FPDF->SetFont('Helvetica', 'B', 12);
        $this->FPDF->TextWithRotation(105, 296.2, '(bit.ly/6h23b)', 2);

        $this->FPDF->SetDash(1, 1);
        $this->FPDF->SetDrawColor(169, 169, 169);
        $this->FPDF->Line(35, 315, 155, 315);
        $this->FPDF->SetDash();

        $this->FPDF->Image('assets/img/banner5.png',45,311);

    }

    private function section_3(){

        //border
        $this->FPDF->SetLineWidth(0.5);
        $this->FPDF->SetXY(35,315);
        $this->FPDF->SetDrawColor(0, 0, 0);
        for ($i = 0; $i < 20; $i++) {
            $this->FPDF->Cell(120, 5, '', 'LR', 1, 1);
        }

        $this->FPDF->Image('assets/img/banner6.png',35,320);

        $this->FPDF->SetXY(45,325);
        $this->FPDF->Cell(30, 5, '', '', 0, 0);
        $this->FPDF->SetFont('Helvetica', '', 12);
        $this->FPDF->Cell(50, 5, 'ARE YOU A REGULAR?', '', 1, 1);
        $this->FPDF->SetXY(35,331);
        $this->FPDF->Cell(15, 5, '', '', 0, 0);
        $this->FPDF->SetFont('Helvetica', '', 12);
        $this->FPDF->Cell(50, 5, 'GET A PERSONALIZED RECEIPT NEXT TIME.', '', 1, 1);

        //checkboxes
        $this->FPDF->SetXY(45,348);
        $this->FPDF->SetFont('Helvetica', '', 11);
        $this->FPDF->Cell(5, 5, '', 1, 0);
        $this->FPDF->Cell(5, 5, '', 0, 0);
        $this->FPDF->Cell(0, 5, 'News Headlines', 0, 1);

        $this->FPDF->Cell(0, 5, '', 0, 1);

        $this->FPDF->SetXY(45,355);
        $this->FPDF->Cell(5, 5, '', 1, 0);
        $this->FPDF->Cell(5, 5, '', 0, 0);
        $this->FPDF->Cell(70, 5, 'My Google Calendar. My user name is: ', 0, 0);
        $this->FPDF->Cell(20, 5, '',1, 0);

        $this->FPDF->SetXY(45,362);
        $this->FPDF->Cell(5, 5, '', 1, 0);
        $this->FPDF->Cell(5, 5, '', 0, 0);
        $this->FPDF->Cell(45, 5, '20% off your next meal at ', 0, 0);
        $this->FPDF->SetFont('Helvetica', 'B', 11);
        $this->FPDF->Cell(30, 5, 'Bread & Butter',0, 1);

        $this->FPDF->Cell(0, 7, '', 0, 1);

        $this->FPDF->Cell(35, 5, '', 0, 1);
        $this->FPDF->Cell(30, 2, '', 0, 0);
        $this->FPDF->SetFont('Helvetica', '', 14);
        $this->FPDF->Cell(37, 5, 'CUSTOMER ID:',0, 0);
        $this->FPDF->Cell(45, 5, '046348632',0, 0);

        $this->FPDF->Code128(40,387,'046348632-5219-21212-52',110,10);

        $this->FPDF->SetLineWidth(1);
        $this->FPDF->Line(40, 405, 150, 405);
        $this->FPDF->Line(40, 420, 150, 420);

        for($i = 0; $i < 6; $i++) {
            $this->FPDF->Cell(0, 5, '', 0, 1);
        }

        $this->FPDF->Cell(0, 6, 'THANK YOU - SEE YOU AGAIN SOON.', 0, 1,'C');
        $this->FPDF->SetLineWidth(0.5);
        $this->FPDF->Cell(0, 5, '', 'LR', 1);
        $this->FPDF->Cell(0, 5, '', 'LRB', 1);



    }

    private function new_blank_line($space, $border, $line_width)
    {
        $this->FPDF->SetLineWidth($line_width);
        $this->FPDF->Cell(0, $space, '', $border, 1);
    }

}