<?php
/**
 * ProductWriterXml.php
 *
 * @copyright B7 Interactive, LLC. All Rights Reserved.
 */

/**
 * Class SearchSpring_Manager_Writer_Product_FileWriter
 *
 * Responsible for writing product data to xml
 *
 * @author Nate Brunette <nate@b7interactive.com>
 */
class SearchSpring_Manager_Writer_Product_FileWriter implements SearchSpring_Manager_Writer_ProductWriter
{
    /**
     * Format for continue response
     */
    const MESSAGE_FORMAT_CONTINUE = '%s|%d';

	/**
	 * XmlWriter object
	 *
	 * @var XMLWriter $xmlWriter
	 */
	private $xmlWriter;

	/**
	 * Holds values the writer needs
	 *
	 * @var SearchSpring_Manager_Writer_Product_FileWriter $writerParams
	 */
	private $writerParams;

	/**
	 * Constructor
	 *
	 * @param XMLWriter $xmlWriter
	 * @param SearchSpring_Manager_Writer_Product_Params_FileWriterParams $writerParams
	 */
	public function __construct(
		XMLWriter $xmlWriter,
		SearchSpring_Manager_Writer_Product_Params_FileWriterParams $writerParams
	) {
		$this->xmlWriter = $xmlWriter;
		$this->writerParams = $writerParams;
	}

	/**
	 * Transform ProductRecords to xml and write to file
	 *
	 * @param SearchSpring_Manager_Entity_RecordsCollection $records
	 *
	 * @return null
	 * @throws UnexpectedValueException
	 */
	public function write(SearchSpring_Manager_Entity_RecordsCollection $records)
	{
		$this->startWriting();

		foreach ($records as $record) {
			if (!is_array($record)) {
				throw new UnexpectedValueException('Record in unknown format');
			}
			$this->xmlWriter->startElement('Product');

			foreach ($record as $key => $value) {
				if (!is_array($value)) {
					$this->xmlWriter->writeElement($key, $value);

					continue;
				}

				// if it's an array, we want to add all elements to the same node with the same key
				foreach ($value as $v) {
					$this->xmlWriter->writeElement($key, $v);
				}
			}

			$this->xmlWriter->endElement();
		}

		$this->endWriting();

        // get the response string
        $response = $this->getResponse();

		return $response;
	}

	/**
	 * Set up stream and setup document if it's the first write
	 */
	private function startWriting()
	{
		$this->xmlWriter->openMemory();

		if (!$this->writerParams->isFirst()) {
			return;
		}

		if (is_file($this->writerParams->getTempFilename())) {
			unlink($this->writerParams->getTempFilename());
		}

		$this->xmlWriter->startDocument('1.0', 'UTF-8');
		$this->xmlWriter->startElement('Products');
	}

	/**
	 * Write to file from memory and cleanup document if it's the last write
	 */
	private function endWriting()
	{
		if ($this->writerParams->isLast()) {
			$this->xmlWriter->writeRaw('</Products>');
		}

		file_put_contents($this->writerParams->getTempFilename(), $this->xmlWriter->flush(), FILE_APPEND);

		if ($this->writerParams->isLast()) {
			rename($this->writerParams->getTempFilename(), $this->writerParams->getFilename());
		}
	}

    /**
     * Get the response string
     *
     * @return string
     */
    private function getResponse()
    {
        $response = self::REGEN_COMPLETE;
        if (!$this->writerParams->isLast()) {
            $response = sprintf(
                self::MESSAGE_FORMAT_CONTINUE,
                self::REGEN_CONTINUE,
                $this->writerParams->getRequestParams()->getCount() + $this->writerParams->getRequestParams()->getOffset()
            );
        }

        return $response;
    }
}
