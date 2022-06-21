<?php

namespace Spatie\SimpleExcel\Tests;

use OpenSpout\Writer\CSV\Writer;
use Spatie\SimpleExcel\SimpleExcelWriter;
use Spatie\Snapshots\MatchesSnapshots;
use Spatie\TemporaryDirectory\TemporaryDirectory;

class SimpleExcelWriterTest extends TestCase
{
    use MatchesSnapshots;

    private TemporaryDirectory $temporaryDirectory;

    private string $pathToCsv;

    public function setUp(): void
    {
        parent::setUp();

        $this->temporaryDirectory = new TemporaryDirectory(__DIR__ . '/temp');

        $this->pathToCsv = $this->temporaryDirectory->path('test.csv');
    }

    /** @test */
    public function it_can_write_a_regular_csv()
    {
        SimpleExcelWriter::create($this->pathToCsv)
            ->addRow([
                'first_name' => 'John',
                'last_name' => 'Doe',
            ])
            ->addRow([
                'first_name' => 'Jane',
                'last_name' => 'Doe',
            ]);

        $this->assertMatchesFileSnapshot($this->pathToCsv);
    }

    /** @test */
    public function add_multiple_rows()
    {
        SimpleExcelWriter::create($this->pathToCsv)
            ->addRows(
                [
                    [
                        'first_name' => 'John',
                        'last_name' => 'Doe',
                    ],
                    [
                        'first_name' => 'Jane',
                        'last_name' => 'Doe',
                    ],
                ]
            );

        $this->assertMatchesFileSnapshot($this->pathToCsv);
    }

    /** @test */
    public function it_can_use_an_alternative_delimiter()
    {
        SimpleExcelWriter::options()
            ->useDelimiter(';')
            ->openCsv($this->pathToCsv)
            ->addRow([
                'first_name' => 'John',
                'last_name' => 'Doe',
            ])
            ->addRow([
                'first_name' => 'Jane',
                'last_name' => 'Doe',
            ]);

        $this->assertMatchesFileSnapshot($this->pathToCsv);
    }

    /** @test */
    public function it_can_write_a_csv_without_a_header()
    {
        SimpleExcelWriter::create($this->pathToCsv)
            ->noHeaderRow()
            ->addRow([
                'first_name' => 'John',
                'last_name' => 'Doe',
            ])
            ->addRow([
                'first_name' => 'Jane',
                'last_name' => 'Doe',
            ]);

        $this->assertMatchesFileSnapshot($this->pathToCsv);
    }

    /** @test */
    public function it_can_get_the_number_of_rows_written()
    {
        $writerWithAutomaticHeader = SimpleExcelWriter::create($this->pathToCsv)
            ->addRow([
                'first_name' => 'John',
                'last_name' => 'Doe',
            ]);

        $this->assertEquals(2, $writerWithAutomaticHeader->getNumberOfRows());

        $writerWithoutAutomaticHeader = SimpleExcelWriter::create($this->pathToCsv)
            ->noHeaderRow()
            ->addRow([
                'first_name' => 'John',
                'last_name' => 'Doe',
            ]);

        $this->assertEquals(1, $writerWithoutAutomaticHeader->getNumberOfRows());
    }

    /** @test */
    public function the_writer_can_get_the_path()
    {
        $writer = SimpleExcelWriter::createCsv($this->pathToCsv);

        $this->assertEquals($this->pathToCsv, $writer->getPath());
    }

    /** @test */
    public function it_allows_setting_the_writer_type_manually()
    {
        $writer = SimpleExcelWriter::createCsv('php://output');

        $this->assertInstanceOf(Writer::class, $writer->getWriter());
    }

    /** @test */
    public function it_can_write_a_csv_without_bom()
    {
        SimpleExcelWriter::options()
            ->withoutBom()
            ->openCsv($this->pathToCsv)
            ->addRow([
                'first_name' => 'Jane',
                'last_name' => 'Doe',
            ]);

        $this->assertMatchesFileSnapshot($this->pathToCsv);
    }
}
