<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

class TicketGenerator extends CI_Controller
{

    private $FPDF;

    public function __construct()
    {
        parent::__construct();

        $params = array('P', 'in');
        $this->load->library('fpdf', $params);

        $this->FPDF = $this->fpdf;
    }

    public function generateTicket()
    {
        $this->FPDF->SetMargins(35, 0, 35);
        $this->FPDF->AddPage('P', array(190, 450));

        $this->FPDF->SetFont('Helvetica', 'B', 12);
        $this->section_1();



    }

    private function section_1()
    {

        //$this->FPDF->Image('image1.jpg');

        $this->new_blank_line(5, 'B', 0.5);
        $this->new_blank_line(5, 'LR', 0.5);

        $this->FPDF->SetDrawColor(169,169,169);
        $this->FPDF->SetDash(1, 1);
        $this->FPDF->Cell(20, 5, '', 'LTR', 0);
        $this->FPDF->SetDash();
        $this->FPDF->SetDrawColor(0,0,0);
        $this->FPDF->Cell(5, 5, '', 0, 0);
        $this->FPDF->Cell(90, 5, '', 'LTR', 0);
        $this->FPDF->Cell(0, 5, '', 'R', 1);

        for ($i = 0; $i < 4; $i++) {

            $this->FPDF->SetDash(1, 1);
            $this->FPDF->SetDrawColor(169,169,169);
            $this->FPDF->Cell(20, 5, '', 'LR', 0);
            $this->FPDF->SetDash();
            $this->FPDF->SetDrawColor(0,0,0);
            $this->FPDF->Cell(5, 5, '', 0, 0);
            $this->FPDF->Cell(90, 5, '', 'LR', 0);
            $this->FPDF->Cell(0, 5, '', 'R', 1);
        }

        $this->FPDF->SetDash(1, 1);
        $this->FPDF->SetDrawColor(169,169,169);
        $this->FPDF->Cell(20, 5, '', 'LBR', 0);
        $this->FPDF->SetDash();
        $this->FPDF->SetDrawColor(0,0,0);
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
        $this->FPDF->SetDrawColor(0,0,0);
        $this->FPDF->Line(35,40,35,10);

        $this->FPDF->Cell(0, 5, '', 'RL', 1);

        $this->FPDF->SetDash(1, 1);
        $this->FPDF->SetDrawColor(169,169,169);
        $this->FPDF->Line(35,70,155,70);
        $this->FPDF->SetDash();
        $this->FPDF->SetDrawColor(0,0,0);

        $this->FPDF->SetTextColor(169,169,169);
        $this->FPDF->SetFont('Helvetica', 'B', 9);
        $this->FPDF->TextWithRotation(45,35,'STAPLE/CUP',90);

        $this->FPDF->SetFont('Helvetica', 'B', 7);
        $this->FPDF->Text(63,15,'DETAILS');
        $this->FPDF->SetDrawColor(169,169,169);
        $this->FPDF->Line(62,17,145,17);

        $this->FPDF->SetTextColor(0,0,0);
        $this->FPDF->SetFont('Helvetica', 'B', 14);

        $this->FPDF->Text(63,25,'MONDAY');
        $this->FPDF->SetFont('Helvetica', '',14);
        $this->FPDF->Text(89,25,'THE');
        $this->FPDF->SetFont('Helvetica', 'B', 14);
        $this->FPDF->Text(102,25,'15/04/2011');

        $this->FPDF->SetFont('Helvetica', 'B', 14);
        $this->FPDF->Text(63,30,'MASTERCARD *3452, EXP 04/13,');

        $this->FPDF->SetFont('Helvetica', 'B', 14);
        $this->FPDF->Text(63,35,'12.15PM,');
        $this->FPDF->SetFont('Helvetica', '', 14);
        $this->FPDF->Text(85,35,'OR');
        $this->FPDF->SetFont('Helvetica', 'B', 14);
        $symbol = substr('£', 1, 1);
        $this->FPDF->Text(93,35,'LUNCHTIME, '.$symbol.'5.14');

        $this->FPDF->SetXY(64,40);
        $this->FPDF->SetDrawColor(169,169,169);
        $this->FPDF->SetTextColor(169,169,169);
        $this->FPDF->SetFont('Helvetica', 'B',7);
        $this->FPDF->Cell(82,5,'NOTES','LTR',1);
        $this->FPDF->SetXY(64,40);
        $this->FPDF->Cell(82,20,'',1);

        $this->FPDF->Output('I', 'report.pdf');

    }

    private function new_blank_line($space, $border, $line_width)
    {
        $this->FPDF->SetLineWidth($line_width);
        $this->FPDF->Cell(0, $space, '', $border, 1);
    }

}